<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 05/07/18
 * Time: 12:22
 */

namespace Combinatoria\OrderWorkflow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {

        $this->orderRepository = $orderRepository;
    }


    public function execute(Observer $observer)
    {
        /** @var $order Order*/


        $order = $this->orderRepository->get($observer->getOrder()->getId());


        if (($order->getState() == Order::STATE_NEW && $order->getStatus() == 'pending') && $order->getPayment()->getMethod() !== 'free'){
            //there is a payment
            $order->setState(Order::STATE_PENDING_PAYMENT);
            $order->setStatus(Order::STATE_PENDING_PAYMENT);
            $order->addStatusToHistory($order->getStatus(), 'Esperando acreditacion del pago');

            $this->orderRepository->save($order);
        }
    }
}