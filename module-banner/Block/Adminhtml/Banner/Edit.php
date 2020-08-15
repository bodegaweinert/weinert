<?php
namespace Combinatoria\Banner\Block\Adminhtml\Banner;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize banner edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Combinatoria_Banner';
        $this->_controller = 'adminhtml_banner';
        parent::_construct();
        if ($this->_isAllowedAction('Combinatoria_Banner::save')) {
            $this->buttonList->update('save', 'label', __('Save Banner'));
        } else {
            $this->buttonList->remove('save');
        }
        if ($this->_isAllowedAction('Combinatoria_Banner::banner_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Banner'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded banner
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('combinatoria_banner_banner')->getId()) {
            return __("Edit Banner '%1'", $this->escapeHtml($this->_coreRegistry->registry('combinatoria_banner_banner')->getTitle()));
        } else {
            return __('New Banner');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}