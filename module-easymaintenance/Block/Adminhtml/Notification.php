<?php

namespace Biztech\Easymaintenance\Block\Adminhtml;

class Notification extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_notification';/*block grid.php directory*/
        $this->_blockGroup = 'Biztech_Easymaintenance';
        $this->_headerText = __('Notification Manager');
        $this->_addButtonLabel = __('Add New Notification'); 
        parent::_construct();
		$this->removeButton('add');
    }
}