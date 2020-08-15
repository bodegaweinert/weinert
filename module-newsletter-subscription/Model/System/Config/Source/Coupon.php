<?php

namespace Combinatoria\NewsletterSubscription\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Combinatoria\NewsletterSubscription\Helper\Data;

class Coupon implements ArrayInterface {

    /**
     * var Data $_helper
     */
    private $_helper;

    /**
     * Constructor
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }


    public function toOptionArray()
    {
        $coupons = array();
        $coupons[] = array('value' => 0, 'label'=> 'No');
        foreach ($this->_helper->getCoupons() as $item) {
            $coupons[] = array('value'=> $item->getId(),'label'=>$item->getName());
        }

        return $coupons;
    }
}