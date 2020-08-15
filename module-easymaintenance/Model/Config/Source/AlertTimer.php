<?php
namespace Biztech\Easymaintenance\Model\Config\Source;

class AlertTimer implements \Magento\Framework\Option\ArrayInterface{

    
    public function __construct( 

    ){

    }
    public function toOptionArray(){
        $options[] = array(
            'label' => '1 hour',
            'value' => '1',
        );
        $options[] = array(
            'label' => '2 hours',
            'value' => '2',
        );
        $options[] = array(
            'label' => '3 hours',
            'value' => '3',
        );
        
        return $options;
    }
}