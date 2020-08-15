<?php

/**
 * Color Status
 *
 * Set the each sales row with a background color on sales grid.
 *
 * @package Igorludgero\Colorstatus
 * @author Igor Ludgero Miura <igor@imaginemage.com>
 * @copyright Copyright (c) 2017 Igor Ludgero Miura (https://www.igorludgero.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Igorludgero\Colorstatus\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class StatusID extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteria;

    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    protected $_statusModel;

    /**
     * StatusID constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $criteria
     * @param \Magento\Sales\Model\Order\Status $statusModel
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria, \Magento\Sales\Model\Order\Status $statusModel, array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->_statusModel = $statusModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare the status color data.
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order = $this->_orderRepository->get($item["entity_id"]);
                $status = $order->getStatus();
                $statusModel = $this->_statusModel->getCollection()->addFieldToFilter("status",$status)->addFieldToSelect(["color"])->getFirstItem();
                $item[$this->getData('name')] = ($statusModel->getColor()) ? $statusModel->getColor() : '';
            }
        }
        return $dataSource;
    }

}