<?php

namespace MercadoPago\Core\Api;

use MercadoPago\Core\Api\Data\NotificationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Charge CRUD interface.
 * @api
 */
interface NotificationRepositoryInterface
{
    /**
     * Create or update a Notification.
     * @param \MercadoPago\Core\Api\Data\NotificationInterface  $notification
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(NotificationInterface $notification);

    /**
     * Retrieve Notification.
     * @param string $id
     * @return \MercadoPago\Core\Api\Data\NotificationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If notification with the specified id does not exist.
     */
    public function get($id);

    /**
     * Retrieve Notification which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \MercadoPago\Core\Api\Data\NotificationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete Notification.
     * @param \MercadoPago\Core\Api\Data\NotificationInterface $notification
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(NotificationInterface $notification);

    /**
     * Delete Notification by ID.
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}