<?php

namespace MercadoPago\Core\Model\System\Config\Source\Cancellation;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class LimitDuration
 */
class LimitDuration implements ArrayInterface {

    public function toOptionArray()
    {
        return array(
            array('value' => '5m' , 'label' => __('5 min')),
            array('value' => '24' , 'label' => __('24 hs')),
            array('value' => '48' , 'label' => __('48 hs')),
            array('value' => '72' , 'label' => __('72 hs')),
            array('value' => '96' , 'label' => __('96 hs')),
            array('value' => '120', 'label' => __('120 hs'))
        );
    }
}