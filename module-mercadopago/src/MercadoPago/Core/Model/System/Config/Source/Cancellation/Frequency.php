<?php

namespace MercadoPago\Core\Model\System\Config\Source\Cancellation;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Frequency
 */
class Frequency implements ArrayInterface {

    public function toOptionArray()
    {
        return array(
            array('value' => '*/5 * * * *' , 'label' => __('5  minutes')),
            array('value' => '0 */1 * * *' , 'label' => __('1  hour')),
            array('value' => '0 */6 * * *' , 'label' => __('6  hours')),
            array('value' => '0 */12 * * *', 'label' => __('12 hours')),
            array('value' => '0 0 */1 * *' , 'label' => __('1  day'))
        );
    }
}