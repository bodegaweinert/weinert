<?php

namespace Combinatoria\OrderWorkflow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

class QuoteSubmitBeforeObserver implements ObserverInterface
{
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

        $order->setAvoidEmails($quote->getAvoidEmails());
        $order->setAvoidCreateCustomer($quote->getAvoidCreateCustomer());

        return $this;
    }
}
