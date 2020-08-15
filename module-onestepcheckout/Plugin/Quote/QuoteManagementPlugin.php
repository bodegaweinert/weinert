<?php

namespace Aheadworks\OneStepCheckout\Plugin\Quote;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Psr\Log\LoggerInterface;

class QuoteManagementPlugin
{
    const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';

    const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const CHARS_DIGITS = '0123456789';


    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Api\Data\RegionInterfaceFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var Random $mathRandom
     */
    protected $mathRandom;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var  \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param Random $mathRandom
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Random $mathRandom,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->objectCopyService = $objectCopyService;
        $this->accountManagement = $accountManagement;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->mathRandom = $mathRandom;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }


    public function afterPlaceOrder(
        \Magento\Quote\Model\QuoteManagement $subject,
        $result
    ){
        $logLevel = 'INFO';
        $this->logger->log($logLevel,'************* (AFTER PLACE ORDER)');
        $this->logger->log($logLevel,'arranca el proceso verificacion y asignacion o creacion del cliente que compro');

        try {
            $orderId = $result;
            $order = $this->orderRepository->get($orderId);

            $this->logger->log($logLevel,'checkeo si la orden NO tiene customerID');

            if (!$order->getAvoidCreateCustomer() && !$order->getCustomerId()){
                $this->logger->log($logLevel,'trato de traer un customer por el email de compra');

                try{
                    $customerEmail = $order->getCustomerEmail();
                    $customer = $this->customerRepository->get($customerEmail);
                } catch (NoSuchEntityException $exception){
                    $this->logger->log($logLevel,'fallo tirando no such entity, entonces creo uno nuevo en base al email de compra');

                    $customer = $this->createCustomer($orderId);
                    $this->checkoutSession->setData('dx_account_created',true);
                }

                $this->logger->log($logLevel,'asigno los datos del customer a la orden (CUSTOMER ID = '. $customer->getId());
                $order->setCustomerId($customer->getId());
                $order->setCustomerFirstname($customer->getFirstname());
                $order->setCustomerLastname($customer->getLastname());
                $order->setCustomerGroupId($customer->getGroupId());
                $order->setCustomerIsGuest(0);

                $this->logger->log($logLevel,'guardo la orden con los nuevos datos');
                $this->orderRepository->save($order);
            }

        } catch (\Exception $exception){
            $this->logger->log($logLevel,'ERROR: '.$exception->getMessage());
        }

        $this->logger->log($logLevel,'termina el proceso');
        $this->logger->log($logLevel,'************* (FIN AFTER PLACE ORDER)');

        return $result;
    }

    private function createCustomer($orderId){

        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId()) {
            throw new AlreadyExistsException(__("This order already has associated customer account"));
        }
        $customerData = $this->objectCopyService->copyFieldsetToTarget(
            'order_address',
            'to_customer',
            $order->getBillingAddress(),
            []
        );
        $addresses = $order->getAddresses();
        foreach ($addresses as $address) {
            $addressData = $this->objectCopyService->copyFieldsetToTarget(
                'order_address',
                'to_customer_address',
                $address,
                []
            );
            /** @var \Magento\Customer\Api\Data\AddressInterface $customerAddress */
            $customerAddress = $this->addressFactory->create(['data' => $addressData]);
            switch ($address->getAddressType()) {
                case QuoteAddress::ADDRESS_TYPE_BILLING:
                    $customerAddress->setIsDefaultBilling(true);
                    break;
                case QuoteAddress::ADDRESS_TYPE_SHIPPING:
                    $customerAddress->setIsDefaultShipping(true);
                    break;
            }

            if (is_string($address->getRegion())) {
                /** @var \Magento\Customer\Api\Data\RegionInterface $region */
                $region = $this->regionFactory->create();
                $region->setRegion($address->getRegion());
                $region->setRegionCode($address->getRegionCode());
                $region->setRegionId($address->getRegionId());
                $customerAddress->setRegion($region);
            }

            $customerAddress->setCustomAttribute('street_number', $addressData['street_number']);
            $customerAddress->setCustomAttribute('street_floor', $addressData['street_floor']);
            $customerAddress->setCustomAttribute('street_apartment', $addressData['street_apartment']);
            $customerAddress->setCustomAttribute('dni', $customerData['dni']);
            $customerAddress->setCustomAttribute('dob', $customerData['dob']);

            $customerAddress->setCustomAttribute('require_invoice', $addressData['require_invoice']);
            $customerAddress->setCustomAttribute('cuit', $addressData['cuit']);
            $customerAddress->setCustomAttribute('business_name', $addressData['business_name']);
            $customerAddress->setCustomAttribute('convenio', $addressData['convenio']);

            $customerData['addresses'][] = $customerAddress;
        }

        /* creo la password */

        do {
            $password = $this->mathRandom->getRandomString(8);
        } while (!$this->validatePassword($password));

        //$password = "pass_1234";

        $this->customerSession->setData('customer_password', $password);

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->customerFactory->create(['data' => $customerData]);
        $customer->setCustomAttribute('customer_dni',$customerData['dni']);
        $account = $this->accountManagement->createAccount($customer, $password);

        return $account;

    }

    private function validatePassword ($password){
        $charsLowersValidated = false;
        $charsUppersValidated = false;
        $charsDigitsValidated = false;

        for ($i = 0; $i < strlen($password); $i++){
            $char = $password[$i];
            if (strpos(self::CHARS_LOWERS,$char) !== false){
                $charsLowersValidated = true;
            }
            if (strpos(self::CHARS_UPPERS,$char) !== false){
                $charsUppersValidated = true;
            }
            if (strpos(self::CHARS_DIGITS,$char) !== false){
                $charsDigitsValidated = true;
            }
        }

        return ($charsLowersValidated && $charsUppersValidated && $charsDigitsValidated);
    }
}