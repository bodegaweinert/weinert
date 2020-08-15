<?php

namespace Biztech\Easymaintenance\Block\Adminhtml\Config\Form\Renderer;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Website extends Field {

    protected $_scopeConfig;
    protected $helper;
    protected $_encryptor;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Biztech\Easymaintenance\Helper\Data $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $html = '';
        $data = $this->_scopeConfig->getValue('easymaintenance/activation/data');
        $ele_value = explode(',', str_replace($data, '', $this->_encryptor->decrypt($element->getValue())));
        $ele_name = $element->getName();
        $ele_id = $element->getId();
        $element->setName($ele_name . '[]');
        $data_info1 = $this->helper->getDataInfo();
        $data_info = (array) $data_info1;
        if (isset($data_info['dom']) && intval($data_info['c']) > 0 && intval($data_info['suc']) == 1) {
            foreach ($this->storeManager->getWebsites() as $website) {
                $url = $this->_scopeConfig->getValue('web/unsecure/base_url');
                $url = $this->helper->getFormatUrl(trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url)));
                foreach ($data_info['dom'] as $web) {
                    if ($web->dom == $url && $web->suc == 1) {
                        $element->setChecked(true);
                        $id = $website->getId();
                        $name = $website->getName();
                        $element->setId($ele_id . '_' . $id);
                        $element->setValue($id);
                        if (in_array($id, $ele_value) !== false) {
                            $element->setChecked(true);
                        }
                        if ($id != 0) {
                            $html .= '<div><label>' . $element->getElementHtml() . ' ' . $name . ' </label></div>';
                        }
                    }
                }
            }
        } else {
            $html = sprintf('<strong class="required">%s</strong>', __('Please enter a valid key'));
        }
        return $html;
    }

}
?>
<html>
    <style type="text/css">
        .required {color: red;}
    </style>
</html>