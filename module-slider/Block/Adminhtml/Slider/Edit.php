<?php
namespace Combinatoria\Slider\Block\Adminhtml\Slider;

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
     * Initialize slider edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Combinatoria_Slider';
        $this->_controller = 'adminhtml_slider';
        parent::_construct();
        if ($this->_isAllowedAction('Combinatoria_Slider::save')) {
            $this->buttonList->update('save', 'label', __('Save Slider'));
        } else {
            $this->buttonList->remove('save');
        }
        if ($this->_isAllowedAction('Combinatoria_Slider::slider_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Slider'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded slider
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('combinatoria_slider_slider')->getId()) {
            return __("Edit Slider '%1'", $this->escapeHtml($this->_coreRegistry->registry('combinatoria_slider_slider')->getTitle()));
        } else {
            return __('New Slider');
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