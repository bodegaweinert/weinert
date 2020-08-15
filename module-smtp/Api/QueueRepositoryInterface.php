<?php
namespace Mageplaza\Smtp\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Mageplaza\Smtp\Api\Data\QueueInterface;

interface QueueRepositoryInterface {
    /**
     * Create or update a queue item.
     *
     * @param \Mageplaza\Smtp\Api\Data\QueueInterface  $queue
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(QueueInterface $queue);

    /**
     * Retrieve Queue item.
     *
     * @param string $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If operatoria with the specified id does not exist.
     */
    public function get($id);

    /**
     * Retrieve queue which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Mageplaza\Smtp\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete Queue item.
     *
     * @param \Mageplaza\Smtp\Api\Data\QueueInterface $queue
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(QueueInterface $queue);

    /**
     * Delete queue item by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}