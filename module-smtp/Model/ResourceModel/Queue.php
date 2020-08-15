<?php

namespace Mageplaza\Smtp\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Queue extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Queue constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        DateTime $date,
        Context $context
    )
    {
        $this->date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('smtp_queue', 'id');
    }


    /**
     * Process Queue data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->isObjectNew()) {
            $object->setSentAt($this->date->date());
        }

        return parent::_beforeSave($object);
    }

}