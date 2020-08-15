<?php
namespace Combinatoria\NewsletterSubscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class Data extends AbstractHelper{

    /**
     * var RuleCollectionFactory $_salesRuleCollection
     */
    private $_salesRuleCollection;

    /**
     * Constructor
     *
     * @param Context $context
     * @param RuleCollectionFactory $salesRuleCollection
     */
    public function __construct(
        Context $context,
        RuleCollectionFactory $salesRuleCollection
    ) {
        parent::__construct($context);
        $this->_salesRuleCollection = $salesRuleCollection;
    }

    public function getCoupons()
    {
        $items = $this->_salesRuleCollection
                      ->create()
                      ->addFieldToFilter('coupon_type', ['gt' => 1])
                      ->load();
        return $items;
    }

}