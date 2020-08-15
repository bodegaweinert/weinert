<?php
namespace Combinatoria\Slider\Model\ResourceModel\Slider;
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
        $this->_init('Combinatoria\Slider\Model\Slider','Combinatoria\Slider\Model\ResourceModel\Slider');
    }
}