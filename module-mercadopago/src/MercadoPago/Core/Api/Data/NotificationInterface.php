<?php

namespace MercadoPago\Core\Api\Data;

/**
 * Interface NotificationInterface
 * @package MercadoPago\Core\Api\Data
 **/

Interface NotificationInterface {
    /**
     * Constants for keys of data array. Identical to the name of the getter in setter case
     */
    const ID            = 'id';
    const ORDER_ID      = 'order_id';
    const MP_PAYMENT_ID = 'mp_payment_id';
    const NOTIFICATION  = 'notification';
    const STATUS        = 'status';
    const APPLIED       = 'applied';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';


    /**
     * Get ID
     * @return int|null
     */
    public function getId();

    /**
     * Get Order ID
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get MP Payment ID
     * @return int|null
     */
    public function getMpPaymentId();

    /**
     * Get Notification
     * @return string|null
     */
    public function getNotification();

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Get Applied
     * @return int|null
     */
    public function getApplied();

    /**
     * Get Created at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Updated at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set ID
     * @param int $id
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setId($id);

    /**
     * Set Order ID
     * @param int $orderId
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setOrderId($orderId);

    /**
     * Set MP Payment ID
     * @param string $mpPaymentId
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setMpPaymentId($mpPaymentId);

    /**
     * Set Notification
     * @param string $notification
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setNotification($notification);

    /**
     * Set Status
     * @param string $status
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setStatus($status);

    /**
     * Set Applied
     * @param int $applied
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setApplied($applied);

    /**
     * Set Created at
     * @param string $createdAt
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set Updated at
     * @param string $updatedAt
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     */
    public function setUpdatedAt($updatedAt);
}