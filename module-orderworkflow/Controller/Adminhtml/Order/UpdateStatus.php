<?php

namespace Combinatoria\OrderWorkflow\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Combinatoria\OrderWorkflow\Helper\Data;

class UpdateStatus extends Action
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Data
    */
    protected $helper;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        Data $helper
    )
    {
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        parent::__construct( $context );
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');

        try {
            /** @var $order Order*/
            $order = $this->orderRepository->get($orderId);


            $order->setState($this->helper->getStateByStatus($status));
            $order->setStatus($status);
            $order->addStatusToHistory($order->getStatus(), 'changed in admin');

            $this->orderRepository->save($order);

            $this->messageManager->addSuccessMessage( __('The order has been successfully saved.') );


        } catch (\Exception $e){
            $this->messageManager->addErrorMessage( $e->getMessage() );
            $this->_redirect('sales/order/view',['order_id'=>$orderId]);
        }

        $this->_redirect('sales/order/view',['order_id'=>$orderId]);
    }
}