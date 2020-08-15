<?php
namespace MercadoPago\Core\Model;

use MercadoPago\Core\Api\NotificationRepositoryInterface;
use MercadoPago\Core\Api\Data\NotificationInterface;
use MercadoPago\Core\Model\ResourceModel\Notification\CollectionFactory;
use MercadoPago\Core\Api\Data\NotificationSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class NotificationRepository
 * @package MercadoPago\Core\Model
 */
class NotificationRepository implements NotificationRepositoryInterface
{
    protected $notificationFactory;
    protected $collectionFactory;
    protected $searchResultsFactory;
    private $resourceModel;

    /**
     * @param \MercadoPago\Core\Model\NotificationFactory $notificationFactory
     * @param \MercadoPago\Core\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
     * @param \MercadoPago\Core\Api\Data\NotificationSearchResultsInterfaceFactory $searchResultsFactory
     * @param \MercadoPago\Core\Model\ResourceModel\Notification $resourceModel
     */
    public function __construct(
        \MercadoPago\Core\Model\NotificationFactory $notificationFactory,
        \MercadoPago\Core\Model\ResourceModel\Notification\CollectionFactory $collectionFactory,
        \MercadoPago\Core\Api\Data\NotificationSearchResultsInterfaceFactory $searchResultsFactory,
        \MercadoPago\Core\Model\ResourceModel\Notification $resourceModel
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(NotificationInterface $notificationInterface)
    {
        try {
            $this->resourceModel->save($notificationInterface);
        }
        catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $notificationInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $notification = $this->notificationFactory->create();
        $notification->load($id);
        if ( !$notification->getId() ) {
            throw new NoSuchEntityException(__('Notification with id "%1" does not exist.', $id));
        }
        return $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(NotificationInterface $notificationInterface)
    {
        try {
            $this->resourceModel->delete( $notificationInterface );
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        $notification = $this->get($id);
        return $this->delete( $notification );
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;
    }
}