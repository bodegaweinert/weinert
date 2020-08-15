<?php
namespace Combinatoria\Slider\Model;

use Combinatoria\Slider\Api\SliderRepositoryInterface;
use Combinatoria\Slider\Api\Data\SliderInterface;
use Combinatoria\Slider\Model\ResourceModel\Slider\CollectionFactory;
use Combinatoria\Slider\Api\Data\SliderSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class SliderRepository
 * @package Combinatoria\Slider\Model
 */
class SliderRepository implements SliderRepositoryInterface
{
    protected $sliderFactory;
    protected $collectionFactory;
    protected $searchResultsFactory;
    private $resourceModel;

    /**
     * @param \Combinatoria\Slider\Model\SliderFactory $sliderFactory
     * @param \Combinatoria\Slider\Model\ResourceModel\Slider\CollectionFactory $collectionFactory
     * @param \Combinatoria\Slider\Api\Data\SliderSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Combinatoria\Slider\Model\ResourceModel\Slider $resourceModel
     */
    public function __construct(
        \Combinatoria\Slider\Model\SliderFactory $sliderFactory,
        \Combinatoria\Slider\Model\ResourceModel\Slider\CollectionFactory $collectionFactory,
        \Combinatoria\Slider\Api\Data\SliderSearchResultsInterfaceFactory $searchResultsFactory,
        \Combinatoria\Slider\Model\ResourceModel\Slider $resourceModel
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(SliderInterface $sliderInterface)
    {
        try {
            //Todo: set slider data via service contract.
            echo 'asdf';
//            $this->resourceModel->save( $sliderInterface );
        }
        catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $sliderInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $slider = $this->sliderFactory->create();
        $slider->load($id);
        if ( !$slider->getId() ) {
            throw new NoSuchEntityException(__('Slider with id "%1" does not exist.', $id));
        }
        return $slider;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SliderInterface $slider)
    {
        try {
            $this->resourceModel->delete( $slider );
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
        $slider = $this->get($id);
        return $this->delete( $slider );
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