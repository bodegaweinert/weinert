<?php
namespace Combinatoria\Banner\Model\ResourceModel\Banner;
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
        $this->_init('Combinatoria\Banner\Model\Banner','Combinatoria\Banner\Model\ResourceModel\Banner');
    }
}