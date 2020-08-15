<?php
/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class SalesQuoteSaveBefore
 * @package Aheadworks\OneStepCheckout\Observer
 */
class SalesQuoteSaveBefore implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $dniIndex             = 0;
        $streetNumberIndex    = 1;
        $streetFloorIndex     = 2;
        $streetApartmentIndex = 3;
        $requireInvoiceIndex  = 4;
        $cuitIndex            = 5;
        $businessNameIndex    = 6;
        $convenioIndex        = 7;
        $dobIndex             = 8;


        $event = $observer->getEvent();

        /** @var Quote $quote */
        $quote = $event->getQuote();

        $shippingAddress = $quote->getShippingAddress();
        $extensionAttributes = $shippingAddress->getExtensionAttributes();

        if ($extensionAttributes){
            $dniAttribute = $extensionAttributes->getCheckoutFields()[$dniIndex];
            $dni = $dniAttribute->getValue();

            $streetNumberAttr = $extensionAttributes->getCheckoutFields()[$streetNumberIndex];
            $streetNumber = $streetNumberAttr->getValue();

            $streetFloorAttr = $extensionAttributes->getCheckoutFields()[$streetFloorIndex];
            $streetFloor = $streetFloorAttr->getValue();

            $streetApartmentAttr = $extensionAttributes->getCheckoutFields()[$streetApartmentIndex];
            $streetApartment = $streetApartmentAttr->getValue();

            $requireInvoiceAttr = $extensionAttributes->getCheckoutFields()[$requireInvoiceIndex];
            $requireInvoce = $requireInvoiceAttr->getValue();

            $cuitAttr = $extensionAttributes->getCheckoutFields()[$cuitIndex];
            $cuit = $cuitAttr->getValue();

            $businessNameAttr = $extensionAttributes->getCheckoutFields()[$businessNameIndex];
            $businessName = $businessNameAttr->getValue();

            $convenioAttr = $extensionAttributes->getCheckoutFields()[$convenioIndex];
            $convenio = $convenioAttr->getValue();

            $dobAttribute = $extensionAttributes->getCheckoutFields()[$dobIndex];
            $dob = $dobAttribute->getValue();

            $quote->getShippingAddress()->setDni($dni);
            $quote->getShippingAddress()->setStreetNumber($streetNumber);
            $quote->getShippingAddress()->setStreetFloor($streetFloor);
            $quote->getShippingAddress()->setStreetApartment($streetApartment);
            $quote->getShippingAddress()->setRequireInvoice($requireInvoce);
            $quote->getShippingAddress()->setCuit($cuit);
            $quote->getShippingAddress()->setBusinessName($businessName);
            $quote->getShippingAddress()->setConvenio($convenio);
            $quote->getShippingAddress()->setDob($dob);

            $quote->getBillingAddress()->setDni($dni);
            $quote->getBillingAddress()->setStreetNumber($streetNumber);
            $quote->getBillingAddress()->setStreetFloor($streetFloor);
            $quote->getBillingAddress()->setStreetApartment($streetApartment);
            $quote->getBillingAddress()->setRequireInvoice($requireInvoce);
            $quote->getBillingAddress()->setCuit($cuit);
            $quote->getBillingAddress()->setBusinessName($businessName);
            $quote->getBillingAddress()->setConvenio($convenio);
            $quote->getBillingAddress()->setDob($dob);
        }

        return $this;
    }
}
