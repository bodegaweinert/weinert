<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) 2017 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Smtp\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Mageplaza\Smtp\Model\ResourceModel\Queue
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Smtp\Model\Queue', 'Mageplaza\Smtp\Model\ResourceModel\Queue');
    }


}
