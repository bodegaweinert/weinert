<?php
namespace Combinatoria\Banner\Api;

use Combinatoria\Banner\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Banner CRUD interface.
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Create or update a Banner.
     *
     * @param \Combinatoria\Banner\Api\Data\BannerInterface  $storepickup
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BannerInterface $banner);

    /**
     * Retrieve Banner.
     *
     * @param string $id
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If storepickup with the specified id does not exist.
     */
    public function get($id);

    /**
     * Retrieve Banners which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Combinatoria\Banner\Api\Data\BannerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete Banner.
     *
     * @param \Combinatoria\Banner\Api\Data\BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BannerInterface $banner);

    /**
     * Delete Banner by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}