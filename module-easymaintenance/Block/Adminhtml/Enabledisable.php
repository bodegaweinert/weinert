<?php

namespace Biztech\Easymaintenance\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Enabledisable extends Field {

    const XML_PATH_ACTIVATION = 'easymaintenance/activation/key';

    protected $_scopeConfig;
    protected $_helper;
    protected $_resourceConfig;
    protected $_web;
    protected $_store;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Biztech\Easymaintenance\Helper\Data $helper, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\Website $web, \Magento\Store\Model\Store $store, array $data = []
    ) {
        $this->_helper = $helper;
        $this->storeManager = $storeManager;
        $this->_web = $web;
        $this->_resourceConfig = $resourceConfig;
        $this->_store = $store;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $websites = $this->_helper->getAllWebsites();
        if (!empty($websites)) {
            $website_id = $this->getRequest()->getParam('website');
            $website = $this->_web->load($website_id);
            if ($website && in_array($website->getWebsiteId(), $websites)) {
                $html = $element->getElementHtml();
            } elseif (!$website_id) {
                $html = $element->getElementHtml();
                $isEnabl = $this->_scopeConfig->getValue('easymaintenance/general/is_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if (!$isEnabl) {
                    $this->_resourceConfig->saveConfig('easymaintenance/general/is_enabled', 0, 'default', 0);
                    $this->_resourceConfig->saveConfig('easymaintenance/timer/timer_enabled', 0, 'default', 0);
                }
            } else {
                $html = sprintf('<strong class="required">%s</strong>', __('Please buy additional domains'));
            }
        } else {
            $websitecode = $this->_request->getParam('website');
            $websiteId = $this->_store->load($websitecode)->getWebsiteId();
            $isenabled = $this->_storeManager->getWebsite($websiteId)->getConfig('easymaintenance/activation/key');
            $modulestatus = $this->_resourceConfig;
            if ($isenabled != null || $isenabled != '') {
                $html = sprintf('<strong class="required">%s</strong>', __('Please select a website'));
                $modulestatus->saveConfig('easymaintenance/general/is_active', 0, 'default', 0);
            } else {
                $html = sprintf('<strong class="required">%s</strong>', __('Please enter a valid key'));
                $modulestatus->saveConfig('easymaintenance/general/is_enabled', 0, 'default', 0);
                $modulestatus->saveConfig('easymaintenance/timer/timer_enabled', 0, 'default', 0);
            }
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