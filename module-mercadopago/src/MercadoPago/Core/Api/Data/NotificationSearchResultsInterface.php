<?php

namespace MercadoPago\Core\Api\Data;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for notifications search results.
 * @api
 */
interface NotificationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get notifications list.
     * @return \MercadoPago\Core\Api\Data\NotificationInterface[]
     */
    public function getItems();

    /**
     * Set notifications list.
     * @param \MercadoPago\Core\Api\Data\NotificationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}