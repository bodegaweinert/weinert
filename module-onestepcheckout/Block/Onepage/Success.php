<?php

namespace Aheadworks\OneStepCheckout\Block\Onepage;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Catalog\Helper\Image;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    const PM_MERCADOPAGO_TICKET  = 'mercadopago_customticket';
    const PM_BANK_TRANSFER       = 'banktransfer';
    const PM_PAYMENT_ON_DELIVERY = 'combinatoria_storepayment';
    const OCA_OPERATORY_DOM_PATH = 'carriers/ocaol/operatoria_domicilio';

    protected $_paymentMethod;

    protected $_order;



    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */

    protected $_addressRenderer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var PaymentHelper $_paymentHelper
     */

    protected $_paymentHelper;

    /**
     * @var CustomerRepositoryInterface $_customerRepository
     */
    protected $_customerRepository;

    /**
     * @var ProductRepositoryInterfaceFactory $_productRepository
     */
    protected $_productRepository;

    /**
     * @var Image $_productImageHelper
     */
    protected $_productImageHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository
     * @param \Magento\Catalog\Helper\Image $productImageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository,
        \Magento\Catalog\Helper\Image $productImageHelper,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession , $orderConfig, $httpContext, $data);
        $this->_customerRepository = $customerRepository;
        $this->_paymentHelper = $paymentHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_addressRenderer = $addressRenderer;
        $this->_productRepository = $productRepository;
        $this->_productImageHelper = $productImageHelper;
        $this->prepareBlockData();
    }

    public function getShowNotification(){
        $order = $this->_getOrder();
        $this->_paymentMethod = $order->getPayment()->getMethod();

        return ($this->_paymentMethod == self::PM_BANK_TRANSFER ||
            $this->_paymentMethod == self::PM_MERCADOPAGO_TICKET ||
            $this->_paymentMethod == self::PM_PAYMENT_ON_DELIVERY);

    }

    public function getNotification(){
        $order = $this->_getOrder();
        $payment = $order->getPayment();
        $notification = '';

        switch ($this->_paymentMethod){
            case self::PM_BANK_TRANSFER: {
                $notification = __('Recordá que para confirmar tu compra debes realizar la transferencia bancaria.');
                break;
            }
            case self::PM_MERCADOPAGO_TICKET: {
                $notification = __('Recordá que para confirmar tu compra debes imprimir el ticket y acercarte a una ventanilla de pago.');
                $notification.= '<a href="'. $this->escapeUrl(urldecode($payment->getAdditionalInformation('activation_uri'))).'"
                                    target="_blank" class="btn-boleto-mercadopago button-primary" style="margin-top:10px;">
                                    '. __('Generate Ticket'). '
                                 </a>';
                break;
            }
            case self::PM_PAYMENT_ON_DELIVERY : {
                $notification = __('Recordá que para confirmar tu compra deberás realizar el pago al momento del retiro.');
                break;
            }
        }

        return $notification;
    }

    public function getCustomerInfo(){
        $order = $this->_getOrder();
        //$customer = $this->_customerRepository->getById($order->getCustomerId());
        $customerInfo = array(
            '<b>'.__('Nombre: ').'</b>'.$order->getShippingAddress()->getFirstname().' '.$order->getShippingAddress()->getLastname(),
            '<b>'.__('Email: ').'</b>'.$order->getCustomerEmail(),
            '<b>'.__('Teléfono: ').'</b>'.$order->getShippingAddress()->getTelephone()
        );

        if ($order->getShippingAddress()->getDni()){
            $customerInfo[] = '<b>'.__('DNI: ').'</b>'.$order->getShippingAddress()->getDni();
        }

        if ($order->getShippingAddress()->getDob()){
            $customerInfo[] = '<b>'.__('Date of birth: ').'</b>'.$order->getShippingAddress()->getDob();
        }

        return $customerInfo;
    }

    public function getOrder(){
        return $this->_getOrder();
    }

    public function getBillingInfo(){
        $order = $this->_getOrder();
        if ($order->getRequireInvoice()){
            $customerInfo = [];
            if ($order->getBillingAddress()->getBusinessName()){
                $customerInfo[]='<b>'.__('Razon Social: ').'</b>'.$order->getBillingAddress()->getBusinessName();
            }
            $customerInfo[]='<b>'.__('CUIT: ').'</b>'.$order->getBillingAddress()->getCuit();
        } else {
            //$customer = $this->_customerRepository->getById($order->getCustomerId());
            $customerInfo = array(
                '<b>'.__('Nombre: ').'</b>'.$order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname()
            );

            if ($order->getBillingAddress()->getDni()){
                $customerInfo[] = '<b>'.__('DNI: ').'</b>'.$order->getBillingAddress()->getDni();
            }
        }

        return $customerInfo;
    }

    public function getCustomerName(){
        $order = $this->_getOrder();
        $customer = $this->_customerRepository->getById($order->getCustomerId());
        return $customer->getFirstname();
    }

    public function getPaymentHtml()
    {
        $order = $this->_getOrder();
        return $this->_paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );
    }

    public function getShippingInfo(){
        $return = '';
        $order = $this->_getOrder();
        $shippingMethod = $order->getShippingMethod();

        if (strpos($shippingMethod, 'ocaol') !== false){
            $ocaOperatoryDom = $this->_getConfigValue(self::OCA_OPERATORY_DOM_PATH, $order->getStoreId());
            if ($shippingMethod == 'ocaol_'.$ocaOperatoryDom){
                $return = $order->getShippingDescription().'<br/><br/><span>Direccion de Entrega</span><div style="margin-top:10px; margin-left: 15px;">'.$this->_getFormattedShippingAddress($order).'</div>';
            } else {
                $return = $order->getShippingDescription();
            }
        } else {
            if (strpos($shippingMethod, 'ownlogisticsservice') !== false){
                $return = $order->getShippingDescription().'<br/><br/><span>Direccion de Entrega</span><div style="margin-top:10px; margin-left: 15px;">'.$this->_getFormattedShippingAddress($order).'</div>';
            } else {
                if ($shippingMethod == 'andreaniestandar_estandar'){
                    $return = $order->getShippingDescription().'<br/><br/><span>Direccion de Entrega</span><div style="margin-top:10px; margin-left: 15px;">'.$this->_getFormattedShippingAddress($order).'</div>';
                } elseif ($shippingMethod == 'andreanisucursal_sucursal'){
                    $return = $order->getShippingDescription();
                } elseif ($shippingMethod == 'freeshipping_freeshipping') {
                    $return = $order->getShippingDescription().'<br/><br/>A la brevedad nos contactaremos para coordinar el envio. <br/><br/><span>Direccion de Entrega</span><div style="margin-top:10px; margin-left: 15px;">'.$this->_getFormattedShippingAddress($order).'</div>';
                }else {
                    $return = $order->getShippingDescription();
                }

            }
        }

        return $return;
    }

    private function _getOrder(){
        if (!$this->_order){
            $this->_order = $this->_checkoutSession->getLastRealOrder();
        }
        return $this->_order;
    }

    private function _getConfigValue($path, $storeId)
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    private function _getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }

    public function getProductImage($productId){
        $product = $this->_productRepository->create()->getById($productId);
        $productUrl = $product->getProductUrl();

        $productImg = $this->_productImageHelper->init($product, 'product_thumbnail_image')->getUrl();

        return $productImg;
    }

    public function getProductUrl($productId){
        $product = $this->_productRepository->create()->getById($productId);
        $productUrl = $product->getProductUrl();

        return $productUrl;
    }

    public function getShowNewCustomerBlock(){
        $accountCreated = $this->_checkoutSession->getData('dx_account_created',true);
        return $accountCreated;
    }

}