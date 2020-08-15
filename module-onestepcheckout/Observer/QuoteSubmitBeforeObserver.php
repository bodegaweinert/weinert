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
use Aheadworks\OneStepCheckout\Helper\Convenio as ConvenioHelper;
/**
 * Class QuoteSubmitBeforeObserver
 * @package Aheadworks\OneStepCheckout\Observer
 */
class QuoteSubmitBeforeObserver implements ObserverInterface
{
    protected $convenioHelper;

    public function __construct(ConvenioHelper $convenioHelper)
    {
        $this->convenioHelper = $convenioHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var OrderInterface $order */
        $order = $event->getOrder();
        /** @var Quote $quote */
        $quote = $event->getQuote();

        if ($quote->getIsVirtual()){
            $dni             = $quote->getBillingAddress()->getDni();
            $streetNumber    = $quote->getBillingAddress()->getStreetNumber();
            $streetFloor     = $quote->getBillingAddress()->getStreetFloor();
            $streetApartment = $quote->getBillingAddress()->getStreetApartment();
            $requireInvoice  = $quote->getBillingAddress()->getRequireInvoice();
            $cuit            = $quote->getBillingAddress()->getCuit();
            $businessName    = $quote->getBillingAddress()->getBusinessName();
            $convenio        = $quote->getBillingAddress()->getConvenio();
            $dob             = $quote->getBillingAddress()->getDob();
        } else {
            $dni             = $quote->getShippingAddress()->getDni();
            $streetNumber    = $quote->getShippingAddress()->getStreetNumber();
            $streetFloor     = $quote->getShippingAddress()->getStreetFloor();
            $streetApartment = $quote->getShippingAddress()->getStreetApartment();
            $requireInvoice  = $quote->getShippingAddress()->getRequireInvoice();
            $cuit            = $quote->getShippingAddress()->getCuit();
            $businessName    = $quote->getShippingAddress()->getBusinessName();
            $convenio        = $quote->getShippingAddress()->getConvenio();
            $dob             = $quote->getShippingAddress()->getDob();

            $order->getShippingAddress()->setDni($dni);
            $order->getShippingAddress()->setStreetNumber($streetNumber);
            $order->getShippingAddress()->setStreetFloor($streetFloor);
            $order->getShippingAddress()->setStreetApartment($streetApartment);
            $order->getShippingAddress()->setRequireInvoice($requireInvoice);
            $order->getShippingAddress()->setCuit($cuit);
            $order->getShippingAddress()->setBusinessName($businessName);
            $order->getShippingAddress()->setConvenio($convenio);
            $order->getShippingAddress()->setDob($dob);
        }

        $order->getBillingAddress()->setDni($dni);
        $order->getBillingAddress()->setStreetNumber($streetNumber);
        $order->getBillingAddress()->setStreetFloor($streetFloor);
        $order->getBillingAddress()->setStreetApartment($streetApartment);
        $order->getBillingAddress()->setRequireInvoice($requireInvoice);
        $order->getBillingAddress()->setCuit($cuit);
        $order->getBillingAddress()->setBusinessName($businessName);
        $order->getBillingAddress()->setConvenio($convenio);
        $order->getBillingAddress()->setDob($dob);

        $order->setAwOrderNote($quote->getAwOrderNote());
        $order->setAwDeliveryDate($quote->getAwDeliveryDate());
        $order->setAwDeliveryDateFrom($quote->getAwDeliveryDateFrom());
        $order->setAwDeliveryDateTo($quote->getAwDeliveryDateTo());
        $order->setRequireInvoice($requireInvoice);
        $order->setConvenio($convenio);

        $order->setRegEspPer($this->convenioHelper->getTaxLabel($convenio));

        if ($quote->getShippingAddress()->getRequireInvoice()) {
            $subtotal = ( $quote->getSubtotalWithDiscount() + $quote->getShippingAmount() ) / 1.21;
            $amount_percentage = $this->convenioHelper->getTaxPercentage($quote->getShippingAddress()->getConvenio());
            $amount = number_format($subtotal * $amount_percentage,2);
            $order->setRegEspImp($amount);
        }

        return $this;
    }
}
