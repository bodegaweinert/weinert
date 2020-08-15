<?php

namespace Combinatoria\Banner\Api\Data;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Banner search results.
 * @api
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get storepickup list.
     *
     * @return \Combinatoria\Banner\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set storepickup list.
     *
     * @param \Combinatoria\Banner\Api\Data\BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}