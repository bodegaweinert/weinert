<?php

namespace Combinatoria\Slider\Api\Data;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Slider search results.
 * @api
 */
interface SliderSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get storepickup list.
     *
     * @return \Combinatoria\Slider\Api\Data\SliderInterface[]
     */
    public function getItems();

    /**
     * Set storepickup list.
     *
     * @param \Combinatoria\Slider\Api\Data\SliderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}