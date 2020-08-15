<?php

namespace MercadoPago\Core\Model;
use MercadoPago\Core\Api\Data\NotificationInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Notification extends AbstractModel implements NotificationInterface, IdentityInterface
{

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'mercadopago_core_notification';

    /**
     * @var string
     */
    protected $_cacheTag = 'mercadopago_core_notification';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mercadopago_core_notification';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MercadoPago\Core\Model\ResourceModel\Notification');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get Order ID
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get MP Payment ID
     * @return int|null
     */
    public function getMpPaymentId()
    {
        return $this->getData(self::MP_PAYMENT_ID);
    }

    /**
     * Get Notification
     * @return string|null
     */
    public function getNotification()
    {
        return $this->getData(self::NOTIFICATION);
    }

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get Applied
     * @return int|null
     */
    public function getApplied()
    {
        return $this->getData(self::APPLIED);
    }

    /**
     * Get Created at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get Updated at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set ID
     * @param int $id
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set Order ID
     * @param int $orderId
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Set MP Payment ID
     * @param string $mpPaymentId
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setMpPaymentId($mpPaymentId)
    {
        return $this->setData(self::MP_PAYMENT_ID, $mpPaymentId);
    }

    /**
     * Set Notification
     * @param string $notification
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setNotification($notification)
    {
        return $this->setData(self::NOTIFICATION, $notification);
    }

    /**
     * Set Status
     * @param string $status
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set Applied
     * @param int $applied
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setApplied($applied)
    {
        return $this->setData(self::APPLIED, $applied);
    }

    /**
     * Set Created at
     * @param string $createdAt
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set Updated at
     * @param string $updatedAt
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}