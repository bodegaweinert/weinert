<?php
/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\ResourceModel\AddressRepository;

/**
 * Class CustomerAddressSaveBefore
 * @package Aheadworks\OneStepCheckout\Observer
 */
class CustomerAddressSaveAfter implements ObserverInterface
{
    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;


    /** constructor
     * @param AddressRepository $addressRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        AddressRepository $addressRepository,
        CustomerRepository $customerRepository
    )
    {
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getAddress();

        /** @var $customer Customer */
        $customer = $this->customerRepository->getById($customerAddress->getCustomerId());

        if ($customerAddress->getId() == $customer->getDefaultShipping() || $customerAddress->isDefaultShipping()){
            $defaultBilling = $this->addressRepository->getById($customer->getDefaultBilling());

            $defaultBilling->setFirstname($customerAddress->getFirstname());
            $defaultBilling->setLastname($customerAddress->getLastname());
            $defaultBilling->setStreet($customerAddress->getStreet());
            $defaultBilling->setPostcode($customerAddress->getPostcode());
            $defaultBilling->setCity($customerAddress->getCity());
            $defaultBilling->setRegionId($customerAddress->getRegionId());
            $defaultBilling->setRegion($customerAddress->getRegion());
            $defaultBilling->setCountryId($customerAddress->getCountryId());
            $defaultBilling->setTelephone($customerAddress->getTelephone());

            $dni = ($customerAddress->getCustomAttribute('dni')?$customerAddress->getCustomAttribute('dni')->getValue():'');
            $streetNumber = ($customerAddress->getCustomAttribute('street_number')?$customerAddress->getCustomAttribute('street_number')->getValue():'');
            $streetFloor = ($customerAddress->getCustomAttribute('street_floor')?$customerAddress->getCustomAttribute('street_floor')->getValue():'');
            $streetApartment = ($customerAddress->getCustomAttribute('street_apartment')?$customerAddress->getCustomAttribute('street_apartment')->getValue():'');
            $requireInvoice = ($customerAddress->getCustomAttribute('require_invoice')?$customerAddress->getCustomAttribute('require_invoice')->getValue():'');
            $cuit = ($customerAddress->getCustomAttribute('cuit')?$customerAddress->getCustomAttribute('cuit')->getValue():'');
            $businessName = ($customerAddress->getCustomAttribute('business_name')?$customerAddress->getCustomAttribute('business_name')->getValue():'');
            $convenio = ($customerAddress->getCustomAttribute('convenio')?$customerAddress->getCustomAttribute('convenio')->getValue():'');
            $dob = ($customerAddress->getCustomAttribute('dob')?$customerAddress->getCustomAttribute('dob')->getValue():'');

            $defaultBilling->setCustomAttribute('dni', $dni);
            $defaultBilling->setCustomAttribute('street_number', $streetNumber);
            $defaultBilling->setCustomAttribute('street_floor', $streetFloor);
            $defaultBilling->setCustomAttribute('street_apartment', $streetApartment);
            $defaultBilling->setCustomAttribute('require_invoice', $requireInvoice);
            $defaultBilling->setCustomAttribute('cuit', $cuit);
            $defaultBilling->setCustomAttribute('business_name', $businessName);
            $defaultBilling->setCustomAttribute('convenio', $convenio);
            $defaultBilling->setCustomAttribute('dob', $dob);

            $this->addressRepository->save($defaultBilling);
        }








        return $this;
    }
}
