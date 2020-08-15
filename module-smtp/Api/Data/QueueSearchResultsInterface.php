<?php

namespace Mageplaza\Smtp\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for queue items search results.
 * @api
 */
interface QueueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Queue list.
     *
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * Set operatoria list.
     *
     * @param \Mageplaza\Smtp\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}