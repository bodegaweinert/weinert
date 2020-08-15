<?php
namespace MercadoPago\Core\Model\ResourceModel\Notification;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MercadoPago\Core\Model\Notification','MercadoPago\Core\Model\ResourceModel\Notification');
    }
}