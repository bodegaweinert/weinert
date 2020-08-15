<?php

namespace Combinatoria\OrderWorkflow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Combinatoria\OrderWorkflow\Model\Workflow;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Payment\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Mageplaza\Smtp\Helper\QueueHelper;
use Psr\Log\LoggerInterface;
use Magento\Variable\Model\Variable;

class SalesOrderSaveBefore implements ObserverInterface
{
    const SALES_REP_NAME  = 'trans_email/ident_sales/name';
    const SALES_REP_EMAIL = 'trans_email/ident_sales/email';
    const SALES_EMAIL_CC  = 'sales_email/cmb_order/cc_copy_to';
    const SALES_EMAIL_BCC = 'sales_email/cmb_order/bcc_copy_to';

    const XML_PATH_ORDER_RECEIVED_EMAIL_TEMPLATE_FIELD    = 'ow_emails/order_received/order_received_template';
    const XML_PATH_ORDER_ACCREDITED_EMAIL_TEMPLATE_FIELD  = 'ow_emails/order_accredited/order_accredited_template';
    const XML_PATH_ORDER_DELIVERED_EMAIL_TEMPLATE_FIELD   = 'ow_emails/order_delivered/order_delivered_template';

    const EMAIL_TYPE_ORDER_RECEIVED   = 1;
    const EMAIL_TYPE_ORDER_ACCREDITED = 2;
    const EMAIL_TYPE_ORDER_DELIVERED  = 3;

    protected $_storeManager;

    protected $_addressRenderer;

    protected $_paymentHelper;

    protected $_smtpQueueHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CustomerRepositoryInterface $_customerRepository
     */
    protected $_customerRepository;

    /**
     * @var  \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    protected $variable;
    /**
     * @param StoreManagerInterface $storeManager
     * @param Renderer $addressRenderer
     * @param Data $paymentHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRepositoryInterface $customerRepository
     * @param QueueHelper $queueHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Renderer $addressRenderer,
        Data $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository,
        QueueHelper $queueHelper,
        LoggerInterface $logger,
        Variable $variable
    )
    {
        $this->_storeManager = $storeManager;
        $this->_addressRenderer = $addressRenderer;
        $this->_paymentHelper = $paymentHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_customerRepository = $customerRepository;
        $this->_smtpQueueHelper = $queueHelper;
        $this->logger = $logger;
        $this->variable = $variable;
    }

    public function execute(Observer $observer)
    {
        $logLevel = 'INFO';
        $this->logger->log($logLevel,'************* (SALES ORDER SAVE BEFORE)');
        $this->logger->log($logLevel,'arranca el proceso que envia mails en los cambios de estado del pedido');

        try {
            /** @var $order Order*/
            $order = $observer->getOrder();

            //SI NO TIENE QUE MANDAR EMAILS, NO HAGO NADA
            if ($order->getAvoidEmails() == 1 || $order->getAvoidEmails() == true){
                return;
            }

            //get the order original data to compare the old status with the new one
            $originalData = $order->getOrigData();
            $data = $order->getData();


            /*TEMPORAL*****************************/
            if ($originalData['status'] == Workflow::STATUS_PAYMENT_ACCREDITED && $data['status'] == Order::STATE_PENDING_PAYMENT){
                $data['status'] = $originalData['status'];
            }
            /**************************************/


            $this->logger->log($logLevel,'comparo los datos viejos contra los nuevos y si tiene o no customer id');
            if (($data['state'] == $originalData['state'] && $data['status'] == $originalData['status']) || ($data['customer_id'] == null)){
                $this->logger->log($logLevel,'no paso las validaciones, no se hace nada');
                return;
            } else {
                $this->logger->log($logLevel,'comparo el estado nuevo contra los que nos interesa mandar mails');

                if ($data['status'] == Order::STATE_PENDING_PAYMENT){

                    $this->logger->log($logLevel,'mando email de pedido reservado');

                    $this->sendEmail(self::EMAIL_TYPE_ORDER_RECEIVED, $order);
                    $observer->getOrder()->setEmailSent(true);

                } elseif ($data['status'] == Workflow::STATUS_PAYMENT_ACCREDITED){
                    $this->logger->log($logLevel,'mando email de pago acreditado');
                    $this->sendEmail(self::EMAIL_TYPE_ORDER_ACCREDITED, $order);
                } elseif ($data['state'] == Workflow::STATE_SHIPPING && $data['status'] == Workflow::STATUS_SHIPPING_DELIVERED){
                    $this->logger->log($logLevel,'mando email de pedido enviado');
                    $this->sendEmail(self::EMAIL_TYPE_ORDER_DELIVERED, $order);
                }
            }
        } catch (\Exception $exception){
            $this->logger->log($logLevel,'ERROR: '.$exception->getMessage());
        }
        $this->logger->log($logLevel,'termina el proceso');
        $this->logger->log($logLevel,'************* (FIN SALES ORDER SAVE BEFORE)');
    }

    private function sendEmail($type, Order $order){
        $cc = null;
        $bcc = null;

        /* Receiver Detail  */
        $receiverInfo = [
            'name' => $order->getCustomerFirstname(),
            'email' => $order->getCustomerEmail()
        ];


        /* Sender Detail  */
        $senderInfo = [
            'name'  => $this->_getConfigValue(self::SALES_REP_NAME, $order->getStoreId()),
            'email' => $this->_getConfigValue(self::SALES_REP_EMAIL, $order->getStoreId()),
        ];

        $store = $this->_storeManager->getStore($order->getStoreId());

        /* Assign values for your template variables  */
        $emailTempVariables = array();
        $emailTempVariables['payment_html'] = $this->getPaymentHtml($order);
        $emailTempVariables['formattedShippingAddress'] = $this->getFormattedShippingAddress($order);
        $emailTempVariables['formattedBillingAddress'] = $this->getFormattedBillingAddress($order);
        $emailTempVariables['step1'] = $this->getStep1($order, $type);
        $emailTempVariables['step2'] = $this->getStep2($order, $type);

        switch ($type){
            case self::EMAIL_TYPE_ORDER_RECEIVED : {
                $templateXmlPath = self::XML_PATH_ORDER_RECEIVED_EMAIL_TEMPLATE_FIELD;
                $cc  = $this->_getConfigValue(self::SALES_EMAIL_CC, $order->getStoreId());
                $bcc = $this->_getConfigValue(self::SALES_EMAIL_BCC, $order->getStoreId());

                break;
            }
            case self::EMAIL_TYPE_ORDER_ACCREDITED: {
                $templateXmlPath = self::XML_PATH_ORDER_ACCREDITED_EMAIL_TEMPLATE_FIELD;
                break;
            }
            case self::EMAIL_TYPE_ORDER_DELIVERED: {
                $templateXmlPath = self::XML_PATH_ORDER_DELIVERED_EMAIL_TEMPLATE_FIELD;
                break;
            }
        }


        $this->_smtpQueueHelper->saveToQueue($templateXmlPath, $order->getId(), $store->getId(), $emailTempVariables, $senderInfo, $receiverInfo, $cc, $bcc);

    }

    function getStep1(Order $order, $type){
        $step1 = '';
        switch ($type){
            case self::EMAIL_TYPE_ORDER_RECEIVED : {

                $paymentMethod = $order->getPayment()->getMethod();
                if ($paymentMethod == 'mercadopago_custom') {
                    $step1 = $this->variable->loadByCode('paso1_neworder_mercadopago_tarjeta')->getHtmlValue();
                }elseif ($paymentMethod == 'mercadopago_customticket') {
                    $step1 = $this->variable->loadByCode('paso1_neworder_mercadopago_ticket')->getHtmlValue();
                }elseif ($paymentMethod == 'banktransfer') {
                    $step1 = $this->variable->loadByCode('paso1_neworder_banktrasfer')->getHtmlValue();
                } elseif ($paymentMethod == 'combinatoria_storepayment') {
                    $step1 = $this->variable->loadByCode('paso1_neworder_storepayment')->getHtmlValue();
                }else{
                    $step1 = $this->variable->loadByCode('paso1_neworder_default')->getHtmlValue();
                }

                break;
            }
            case self::EMAIL_TYPE_ORDER_ACCREDITED: {
                $step1 = $this->variable->loadByCode('paso1_accredited')->getHtmlValue();

                break;
            }
            case self::EMAIL_TYPE_ORDER_DELIVERED: {
                $step1 = $this->variable->loadByCode('paso1_delivered')->getHtmlValue();
                break;
            }
        }

        return $step1;
    }

    function getStep2(Order $order, $type){
        $step2 = '';
        switch ($type){
            case self::EMAIL_TYPE_ORDER_RECEIVED : {

                $shippingMethod = $order->getShippingMethod();
                $oca_sucursal = $this->_getConfigValue('carriers/ocaol/operatoria_sucursales', $order->getStoreId());
                $oca_dom = $this->_getConfigValue('carriers/ocaol/operatoria_domicilio', $order->getStoreId());
                $storepickup = 'storepickup_storepickup';


                if ($shippingMethod == 'ocaol_'.$oca_sucursal) {
                    $step2 = '2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.';
                } elseif ($shippingMethod == 'ocaol_'.$oca_dom) {
                    $step2 = '2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.';
                } elseif ($shippingMethod == $storepickup) {
                    $step2 = $this->variable->loadByCode('paso2_storepickup')->getHtmlValue();
                } elseif (strpos($shippingMethod, 'ownlogisticsservice') !== false) {
                    $step2 = $this->variable->loadByCode('paso2_ownlogisticsservice')->getHtmlValue();
                } elseif (strpos($shippingMethod, 'andreani') !== false){
                    $step2 = $this->variable->loadByCode('paso2_andreani')->getHtmlValue();
                } elseif ($shippingMethod == 'freeshipping_freeshipping'){
                    $step2 = $this->variable->loadByCode('paso2_default')->getHtmlValue();
                }else{
                    $step2 = $this->variable->loadByCode('paso2_default')->getHtmlValue();
                }

                break;
            }
            case self::EMAIL_TYPE_ORDER_ACCREDITED: {
                $step2 = '';
                break;
            }
            case self::EMAIL_TYPE_ORDER_DELIVERED: {
                $step2 = '';
                break;
            }
        }

        return $step2;
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }


    protected function getFormattedBillingAddress($order)
    {
        if ($order->getRequireInvoice()){
            $customerInfo = '';
            if ($order->getBillingAddress()->getBusinessName()){
                $customerInfo.='Razón Social: '.$order->getBillingAddress()->getBusinessName().'<br/>';
            }
            $customerInfo.='CUIT: '.$order->getBillingAddress()->getCuit();

        } else {
            $customer = $this->_customerRepository->getById($order->getCustomerId());
            $customerInfo = $customer->getFirstname() .' '. $customer->getLastname();
            if ($customer->getCustomAttribute('customer_dni')) {
                $customerInfo .= '<br/> DNI: ' . $customer->getCustomAttribute('customer_dni')->getValue();
            }
        }

        return $customerInfo;
    }


    /**
     * Get payment info block as html
     *
     * @param Order $order
     * @return string
     */
    protected function getPaymentHtml(Order $order)
    {
        $paymenthtml =  $this->_paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );

        $paymenthtml = str_replace('&lt;','<',$paymenthtml);
        $paymenthtml = str_replace('&gt;','>',$paymenthtml);
        $paymenthtml = str_replace('&amp;','&',$paymenthtml);
        $paymenthtml = str_replace('&quot;','"',$paymenthtml);


        return $paymenthtml;
    }

    private function _getConfigValue($path, $storeId)
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}