<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Controller\Adminhtml\Analytics;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class Track extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
	/**
	 * @var OrderManagementInterface
	 */
	protected $orderManagement;
	
	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $orderRepository;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param Context $context
	 * @param Filter $filter
	 * @param CollectionFactory $collectionFactory
	 * @param OrderManagementInterface $orderManagement
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 */
	public function __construct
	(
		Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		OrderManagementInterface $orderManagement,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	) 
	{
		parent::__construct($context, $filter);
		
		/**
		 * Set collection factory 
		 * 
		 * @var CollectionFactory
		 */
		$this->collectionFactory = $collectionFactory;
		
		/**
		 * Set order management 
		 * 
		 * @var OrderManagementInterface
		 */
		$this->orderManagement = $orderManagement;
		
		/**
		 * Set Measurement Protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set order repository 
		 * 
		 * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
		 */
		$this->orderRepository = $orderRepository;
	}
	
	/**
	 * Mass action
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction::massAction()
	 */
	protected function massAction(AbstractCollection $collection) 
	{
		/**
		 * Success log
		 * 
		 * @var array $success
		 */
		$success = [];
		
		/**
		 * Failure log 
		 * 
		 * @var array $failure
		 */
		$failure = [];

        foreach ($collection->getAllIds() as $id)
        {
        	if (false !== $this->protocol->purchaseById($id))
        	{
        		$success[] = __("Transaction: {$this->orderRepository->get($id)->getIncrementId()} successfully sent to Google Analytics");
        	}
        	else 
        	{
        		$failure[] = __("Failed to send transaction: {$id} to Google Analytics");
        	}
        }
        
        foreach ($success as $message)
        {
        	$this->messageManager->addSuccessMessage($message);
        }
        
        foreach ($failure as $message)
        {
        	$this->messageManager->addErrorMessage($message);
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $resultRedirect->setPath('sales/order/index');
        
        return $resultRedirect;
    }

    protected function _isAllowed() 
    {
        return true;
    }
}