<?php

namespace Aheadworks\OneStepCheckout\Plugin\Quote;

class ShippingAddressManagement
{

    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address,
        $useForShipping = false
    ) {

        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                $address->setDni($extAttributes->getDni());
                $address->setStreetNumber($extAttributes->getStreetNumber());
                $address->setStreetFloor($extAttributes->getStreetFloor());
                $address->setStreetApartment($extAttributes->getStreetApartment());
                $address->setRequireInvoice($extAttributes->getRequireInvoice());
                $address->setCuit($extAttributes->getCuit());
                $address->setBusinessName($extAttributes->getBusinessName());
                $address->setConvenio($extAttributes->getConvenio());
                $address->setDob(date("Y-m-d",strtotime(str_replace('/', '-', $extAttributes->getDob()))));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }

        }

    }
}