<?php

namespace MercadoPago\Core\Model\System\Config\Source\Notification;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Frequency
 */
class WaitingFrequency implements ArrayInterface {

    public function toOptionArray()
    {
        return array(
            array('value' => '1' , 'label' => __('1  second')),
            array('value' => '5' , 'label' => __('5  seconds')),
            array('value' => '10', 'label' => __('10 seconds')),
            array('value' => '15', 'label' => __('15 seconds')),
            array('value' => '20', 'label' => __('20 seconds')),
            array('value' => '25', 'label' => __('25 seconds')),
            array('value' => '30', 'label' => __('30 seconds'))
        );
    }
}