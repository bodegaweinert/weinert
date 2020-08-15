<?php
namespace MercadoPago\Core\Cron;

use Combinatoria\OrderWorkflow\Model\Workflow;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order;
use MercadoPago\Core\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;
use MercadoPago\Core\Model\NotificationRepository;

class OrderStatusControl {


    /**
     * @var NotificationCollectionFactory $notificationCollectionFactory
     */
    private $_notificationCollectionFactory;

    /**
     * @var NotificationRepository $notificationRepository
     */
    private $_notificationRepository;

    /**
     * @var OrderRepository $orderRepository
     */
    private $_orderRepository;


    /**
     * Constructor
     *
     * @param NotificationCollectionFactory $notificationCollectionFactory
     * @param NotificationRepository $notificationRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        NotificationCollectionFactory $notificationCollectionFactory,
        NotificationRepository $notificationRepository,
        OrderRepository $orderRepository
    ) {
        $this->_notificationCollectionFactory = $notificationCollectionFactory;
        $this->_notificationRepository = $notificationRepository;
        $this->_orderRepository = $orderRepository;
    }


    /**
     * Execute the cron
     */
    public function execute(){
        $notifications = $this->_notificationCollectionFactory
                            ->create()
                            ->addFieldToFilter('status', ['eq' => 'approved'])
                            ->addFieldToFilter('applied', ['eq' => '0']);

        /** @var $notification \MercadoPago\Core\Model\Notification*/
        foreach ($notifications as $notification){
            $orderId = $notification->getOrderId();

            $order = $this->_orderRepository->get($orderId);
            if ($order->getStatus() == Order::STATE_PENDING_PAYMENT || $order->getStatus() == 'contabilium_pending') {
                $order->setStatus(Workflow::STATUS_PAYMENT_ACCREDITED);
                $order->setState(Workflow::STATE_PAYMENT_ACCREDITED);
                $this->_orderRepository->save($order);
            }

            $notification->setApplied(1);
            $this->_notificationRepository->save($notification);
        }
    }
}