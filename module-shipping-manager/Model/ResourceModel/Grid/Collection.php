<?php

namespace Combinatoria\ShippingManager\Model\ResourceModel\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Combinatoria\OrderWorkflow\Model\Workflow;

/**
 * Class Collection
 * Collection for displaying grid of sales documents
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection implements SearchResultInterface
{

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param null|\Zend_Db_Adapter_Abstract $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param string|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */


    protected function _initSelect()
    {
        parent::_initSelect();

        $orders = $this->_getFilteredOrders();

        $this->addFieldToFilter('entity_id', ['in' => [$orders]]);

        return $this;
    }

    /**
     * @return array
     */
    protected function _getFilteredOrders(){

        $result = [];

        $orders = $this->_conn->fetchAll(
            'SELECT entity_id 
                  FROM sales_order 
                  WHERE status IN ("'.Workflow::STATUS_PAYMENT_ACCREDITED.'","'.Workflow::STATUS_SHIPPING_PENDING.'","contabilium_accepted");'
        );

        foreach ($orders as $key => $value){
            $result[] = $value;
        }

        /* agrego -1 a la lista de los entity_id para que si no devuelve nada la consulta, la grilla este vacia*/
        $result[] = -1;

        return $result;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }


    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
