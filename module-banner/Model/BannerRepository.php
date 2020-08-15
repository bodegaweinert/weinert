<?php
namespace Combinatoria\Banner\Model;

use Combinatoria\Banner\Api\BannerRepositoryInterface;
use Combinatoria\Banner\Api\Data\BannerInterface;
use Combinatoria\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Combinatoria\Banner\Api\Data\BannerSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class BannerRepository
 * @package Combinatoria\Banner\Model
 */
class BannerRepository implements BannerRepositoryInterface
{
    protected $bannerFactory;
    protected $collectionFactory;
    protected $searchResultsFactory;
    private $resourceModel;

    /**
     * @param \Combinatoria\Banner\Model\BannerFactory $bannerFactory
     * @param \Combinatoria\Banner\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
     * @param \Combinatoria\Banner\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Combinatoria\Banner\Model\ResourceModel\Banner $resourceModel
     */
    public function __construct(
        \Combinatoria\Banner\Model\BannerFactory $bannerFactory,
        \Combinatoria\Banner\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
        \Combinatoria\Banner\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory,
        \Combinatoria\Banner\Model\ResourceModel\Banner $resourceModel
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(BannerInterface $bannerInterface)
    {
        try {
            //Todo: set banner data via service contract.
            echo 'asdf';
//            $this->resourceModel->save( $bannerInterface );
        }
        catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $bannerInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $banner = $this->bannerFactory->create();
        $banner->load($id);
        if ( !$banner->getId() ) {
            throw new NoSuchEntityException(__('Banner with id "%1" does not exist.', $id));
        }
        return $banner;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BannerInterface $banner)
    {
        try {
            $this->resourceModel->delete( $banner );
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
        $banner = $this->get($id);
        return $this->delete( $banner );
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