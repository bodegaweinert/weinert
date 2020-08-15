<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class CheckoutSubmitBeforeObserver
 * @package Aheadworks\OneStepCheckout\Observer
 */
class CheckoutSubmitBeforeObserver implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $quote = $this->quoteRepository->get($observer->getQuote()->getId());

        if (!$quote->getIsVirtual()) {
            $dni = $quote->getShippingAddress()->getDni();
            $streetNumber = $quote->getShippingAddress()->getStreetNumber();
            $streetFloor = $quote->getShippingAddress()->getStreetFloor();
            $streetApartment = $quote->getShippingAddress()->getStreetApartment();
            $requireInvoice = $quote->getShippingAddress()->getRequireInvoice();
            $cuit = $quote->getShippingAddress()->getCuit();
            $businessName = $quote->getShippingAddress()->getBusinessName();
            $convenio = $quote->getShippingAddress()->getConvenio();
            $dob = $quote->getShippingAddress()->getDob();

            if (!$quote->getBillingAddress()->getDni()) {
                $quote->getBillingAddress()->setDni($dni);
            }
            if (!$quote->getBillingAddress()->getStreetNumber()) {
                $quote->getBillingAddress()->setStreetNumber($streetNumber);
            }
            if (!$quote->getBillingAddress()->getStreetFloor()) {
                $quote->getBillingAddress()->setStreetFloor($streetFloor);
            }
            if (!$quote->getBillingAddress()->getStreetApartment()) {
                $quote->getBillingAddress()->setStreetApartment($streetApartment);
            }
            if (!$quote->getBillingAddress()->getRequireInvoice()) {
                $quote->getBillingAddress()->setRequireInvoice($requireInvoice);
            }
            if (!$quote->getBillingAddress()->getCuit()) {
                $quote->getBillingAddress()->setCuit($cuit);
            }
            if (!$quote->getBillingAddress()->getBusinessName()) {
                $quote->getBillingAddress()->setBusinessName($businessName);
            }
            if (!$quote->getBillingAddress()->getConvenio()) {
                $quote->getBillingAddress()->setConvenio($convenio);
            }
            if (!$quote->getBillingAddress()->getDob()) {
                $quote->getBillingAddress()->setDob($dob);
            }

            $this->quoteRepository->save($quote);
        }

        return $this;
    }
}
