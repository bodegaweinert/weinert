<?php

namespace Combinatoria\OrderWorkflow\Block\Adminhtml\Order;

use Combinatoria\OrderWorkflow\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class View {

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Data $helper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Data $helper
    )
    {
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
    }

    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view)
    {
        /** @var Order $order*/
        $order = $this->orderRepository->get($view->getOrderId());

        $statusForButton = $this->helper->getStatusesForButton($order->getStatus());

        if ($statusForButton['next'] !=  null ) {

            $view->addButton(
                'order_change_status',
                [
                    'label' => 'Cambiar Estado',
                    'class' => 'add',
                    'button_class' => '',
                    'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
                    'options' => $this->getOptionsForButton($statusForButton['options'], $view)
                ]
            );
        }


        $view->removeButton('order_creditmemo');
        $view->removeButton('void_payment');
        $view->removeButton('order_hold');
        $view->removeButton('order_unhold');
        $view->removeButton('order_invoice');
        $view->removeButton('order_ship');
        $view->removeButton('order_reorder');
        //$view->removeButton('order_cancel');
        $view->removeButton('send_notification');
        $view->removeButton('accept_payment');
        $view->removeButton('deny_payment');
        $view->removeButton('get_review_payment_update');
    }


    public function getOptionsForButton($options, $view){
        $buttonOptions = [];
        foreach ($options as $option){
            $url = $view->getUrl('cmb_ow/order/updateStatus',['id'=>$view->getOrderId(), 'status' => $option['code']]);
            $buttonOptions[] =  [
                'label' => __($option['label']),
                'onclick' => "confirmSetLocation('" . __('Estas por cambiar el estado del pedido a "'.$option['label'].'". Deseas continuar?') . "', '" . $url . "')",
                'default' => false,
            ];
        }
        return $buttonOptions;
    }
}