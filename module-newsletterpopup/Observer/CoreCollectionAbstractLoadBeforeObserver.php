<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\Newsletterpopup\Helper\DataFactory;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class CoreCollectionAbstractLoadBeforeObserver implements ObserverInterface
{
    /**
     * @var DataFactory
     */
    protected $_dataHelperFactory;

    /**
     * @var DateTimeFactory
     */
    protected $_dateFactory;

    /**
     * @param DataFactory $dataHelper [description]
     * @param DateTimeFactory $date
     */
    public function __construct(
        DataFactory $dataHelperFactory,
        DateTimeFactory $dateFactory
    ) {
        $this->_dataHelperFactory = $dataHelperFactory;
        $this->_dateFactory = $dateFactory;
    }

    public function execute(Observer $observer)
    {
        $collection = $observer->getCollection();

        // Check of moduleEnabled must be after checking of collection type
        if ($collection instanceof \Magento\SalesRule\Model\ResourceModel\Rule\Collection
            && $this->_dataHelperFactory->create()->moduleEnabled()
        ) {
            $select = $collection->getSelect();
            $conditions = $select->getPart(\Zend_Db_Select::WHERE);

            if (false === strpos(implode('', $conditions), 'rule_coupons')) {
                return $this;
            }

            /**
             * We will use GMT datetime for defining coupon expiration date
             * This will allow us to more accurately checking
             * coupon expiration period in depending on
             * the time zone of current customer
             */
            $expirationDatePattern = '/(\(\s?rule_coupons\.expiration_date\s+is\s+null\s+or\s+rule_coupons\.expiration_date\s?\>\=\s?[\'\"]\d{4}-\d{1,2}-\d{1,2}[\'\"].*\))/isU';
            $expirationDateCond = "((rule_coupons.np_expiration_date is not null AND (rule_coupons.np_expiration_date >= '"
                . $this->_dateFactory->create()->gmtDate()
                . "')) OR (rule_coupons.np_expiration_date is null AND $1))";

            /**
             * This is default magento condition
             * for validation of coupon expiration date
             * It will be use when our custom expiration date is null
             */
            $toDatePattern = '/(\(to_date\s+is\s+null\s+or\s+to_date\s?\>\=\s?[\'\"]\d{4}-\d{1,2}-\d{1,2}[\'\"].*\))/isU';
            $toDateCond = "(rule_coupons.np_expiration_date is not null"
                . " OR (rule_coupons.np_expiration_date is null AND $1)"
                . ")";

            foreach ($conditions as $key => $condition) {
                $conditions[$key] = preg_replace(
                    /* patterns */
                    [
                        $expirationDatePattern,
                        $toDatePattern,
                    ],
                    /* replace to */
                    [
                        $expirationDateCond,
                        $toDateCond,
                    ],
                    $condition
                );
            }

            $select->setPart(\Zend_Db_Select::WHERE, $conditions);
        }

        return $this;
    }
}
