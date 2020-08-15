<?php

namespace Combinatoria\NewsletterSubscription\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CodeFormat implements ArrayInterface {


    public function toOptionArray()
    {
        return array(
            array('value' => 'alphanum', 'label' => __('Alphanumeric')),
            array('value' => 'alpha', 'label' => __('Alphabetical')),
            array('value' => 'numm', 'label' => __('Numeric'))
        );
    }
}