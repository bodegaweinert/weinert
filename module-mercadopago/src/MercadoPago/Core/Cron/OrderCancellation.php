<?php
namespace MercadoPago\Core\Cron;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Backend\Block\Store\Switcher;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Zend\Validator\Date;

class OrderCancellation {

    /**
     * @var ScopeConfigInterface $_scopeConfig
     */
    private $_scopeConfig;

    /**
     * @var OrderCollectionFactory $orderCollectionFactory
     */
    private $_orderCollectionFactory;

    /**
     * @var OrderManagementInterface $orderManager
     */
    private $_orderManager;

    /**
     * @var DateTime
     */
    private $_date;

    private $_scopeCode;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderManagementInterface $orderManager
     * @param Switcher $switcher
     * @param DateTime $date
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderCollectionFactory $orderCollectionFactory,
        OrderManagementInterface $orderManager,
        Switcher $switcher,
        DateTime $dateTime
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderManager = $orderManager;
        $this->_scopeCode = $switcher->getWebsiteId();
        $this->_date = $dateTime;
    }

    public function execute(){
        $cancellationAvailable = $this->_scopeConfig->getValue(
            \MercadoPago\Core\Helper\Data::XML_PATH_CANCELLATION_AVAILABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->_scopeCode
        );

        $automaticCancellationAvailable = $this->_scopeConfig->getValue(
            \MercadoPago\Core\Helper\Data::XML_PATH_CANCELLATION_AUTOMATIC_AVAILABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->_scopeCode
        );

        if ($cancellationAvailable && $automaticCancellationAvailable){

            $pendingOrders = $this->_orderCollectionFactory
                                ->create()
                                ->addAttributeToFilter('state', ['eq' => 'pending_payment']);

            /** @var Order $order */
            foreach ($pendingOrders as $order){
                $paymentMethod = $order->getPayment()->getMethod();
                $createdAt = $order->getCreatedAt();

                if ($paymentMethod == 'mercadopago_custom' || $paymentMethod == 'mercadopago_customticket'){

                    $orderLimitDuration = $this->_scopeConfig->getValue(
                        \MercadoPago\Core\Helper\Data::XML_PATH_LIMIT_ORDER_DURATION,
                        \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                        $this->_scopeCode
                    );
                    $currentDate = $this->_date->date();

                    $mindiff = round((strtotime($currentDate) - strtotime($createdAt))/60, 1);
                    $hourdiff = round((strtotime($currentDate) - strtotime($createdAt))/3600, 1);

                    /*PARA TEST SOLAMENTE*/
                    if ($orderLimitDuration == '5m'){
                        if ($mindiff > 5){
                            try{
                                $this->_orderManager->cancel($order->getId());
                            } catch (\Exception $e){
                                /* @todo : hacer algo al respecto, loguear o algo */
                            }
                        }
                    } else {
                        /* ESTE ES EL QUE VALE */
                        if ($hourdiff > $orderLimitDuration){
                            try{
                                $this->_orderManager->cancel($order->getId());
                            } catch (\Exception $e){
                                /* @todo : hacer algo al respecto, loguear o algo */
                            }
                        }
                    }
                }
            }
        }
    }
}