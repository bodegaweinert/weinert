<?php
namespace Biztech\Easymaintenance\Model\Config\Source;

class Enabledisable implements \Magento\Framework\Option\ArrayInterface{
    protected $_helper;
    
    public function __construct( 
         \Magento\Framework\ObjectManagerInterface $interface,
         \Biztech\Easymaintenance\Helper\Data $helperdata
    ){
        $this->objectManager = $interface;
        $this->_helper= $helperdata;
    }
    public function toOptionArray(){
        $options = array(
            //array('value' => 0, 'label'=>$this->_helper->__('No')),
            array('value' => 0, 'label'=>__('No')),    
        );
        //$websites = Mage::helper('auspost')->getAllWebsites();
        $websites = $this->_helper->getAllWebsites();
        if(!empty($websites)){
           //$options[] = array('value' => 1, 'label'=>$this->_helper->__('Yes'));
           $options[] = array('value' => 1, 'label'=>__('Yes'));
        }
        return $options;
    }
}