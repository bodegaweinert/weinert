<?php
namespace Combinatoria\Slider\Model\ResourceModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Slider
 * @package Combinatoria\Slider\Model\ResourceModel
 */
class Slider extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context,$resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('combinatoria_slider','id');
    }

    /**
     * Process Slider data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Combinatoria\Slider\Model\Slider $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getId()) {

            $select->where(
                'is_active = ?',
                1
            )->limit(1 );
        }

        return $select;
    }
}