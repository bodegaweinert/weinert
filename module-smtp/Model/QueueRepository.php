<?php
namespace Mageplaza\Smtp\Model;

use Mageplaza\Smtp\Api\QueueRepositoryInterface;
use Mageplaza\Smtp\Api\Data\QueueInterface;
use Mageplaza\Smtp\Model\ResourceModel\Queue\CollectionFactory;
use Mageplaza\Smtp\Api\Data\QueueSearchResultsInterfaceFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class QueueRepository implements QueueRepositoryInterface
{
    protected $queueFactory;
    protected $collectionFactory;
    protected $searchResultsFactory;
    private $resourceModel;

    /**
     * @param \Mageplaza\Smtp\Model\QueueFactory $queueFactory
     * @param \Mageplaza\Smtp\Model\ResourceModel\Queue\CollectionFactory $collectionFactory
     * @param \Mageplaza\Smtp\Api\Data\QueueSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Mageplaza\Smtp\Model\ResourceModel\Queue $resourceModel
     */
    public function __construct(
        \Mageplaza\Smtp\Model\QueueFactory $queueFactory,
        \Mageplaza\Smtp\Model\ResourceModel\Queue\CollectionFactory $collectionFactory,
        \Mageplaza\Smtp\Api\Data\QueueSearchResultsInterfaceFactory $searchResultsFactory,
        \Mageplaza\Smtp\Model\ResourceModel\Queue $resourceModel
    ) {
        $this->queueFactory = $queueFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(QueueInterface $queueInterface)
    {
        try {
            $this->resourceModel->save($queueInterface);
            return $queueInterface->getId();
        }
        catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $queue = $this->queueFactory->create();
        $queue->load($id);
        if ( !$queue->getId() ) {
            throw new NoSuchEntityException(__('Queue with id "%1" does not exist.', $id));
        }
        return $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QueueInterface $queue)
    {
        try {
            $this->resourceModel->delete( $queue );
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
        $queue = $this->get($id);
        return $this->delete( $queue );
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