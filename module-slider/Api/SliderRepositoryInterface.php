<?php
namespace Combinatoria\Slider\Api;

use Combinatoria\Slider\Api\Data\SliderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Slider CRUD interface.
 * @api
 */
interface SliderRepositoryInterface
{
    /**
     * Create or update a Slider.
     *
     * @param \Combinatoria\Slider\Api\Data\SliderInterface  $storepickup
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SliderInterface $slider);

    /**
     * Retrieve Slider.
     *
     * @param string $id
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If storepickup with the specified id does not exist.
     */
    public function get($id);

    /**
     * Retrieve Sliders which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Combinatoria\Slider\Api\Data\SliderSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete Slider.
     *
     * @param \Combinatoria\Slider\Api\Data\SliderInterface $slider
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SliderInterface $slider);

    /**
     * Delete Slider by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}