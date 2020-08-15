<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMetaProvider;
use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Layout\MultilineModifier;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\Merger;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\FieldRowsSorter;
use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class AddressAttributes
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class AddressAttributes implements LayoutProcessorInterface
{
    /**
     * @var AttributeMetaProvider
     */
    private $attributeMataProvider;

    /**
     * @var Merger
     */
    private $attributeMerger;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FieldRowsSorter
     */
    private $rowsSorter;

    /**
     * @var MultilineModifier
     */
    private $multilineModifier;

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @param AttributeMetaProvider $attributeMataProvider
     * @param Merger $attributeMerger
     * @param ArrayManager $arrayManager
     * @param FieldRowsSorter $rowsSorter
     * @param MultilineModifier $multilineModifier
     * @param ModuleConfig $config
     */
    public function __construct(
        AttributeMetaProvider $attributeMataProvider,
        Merger $attributeMerger,
        ArrayManager $arrayManager,
        FieldRowsSorter $rowsSorter,
        MultilineModifier $multilineModifier,
        ModuleConfig $config
    ) {
        $this->attributeMataProvider = $attributeMataProvider;
        $this->attributeMerger = $attributeMerger;
        $this->arrayManager = $arrayManager;
        $this->rowsSorter = $rowsSorter;
        $this->multilineModifier = $multilineModifier;
        $this->config = $config;

    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $shippingAddressFieldRowsPath = 'components/checkout/children/shippingAddress/'
            . 'children/shipping-address-fieldset/children';
        $shippingAddressFieldRowsLayout = $this->arrayManager->get($shippingAddressFieldRowsPath, $jsLayout);
        if ($shippingAddressFieldRowsLayout) {
            if (!$this->config->isBusinessInformationEnabled()){
                unset($shippingAddressFieldRowsLayout['business-field-row']);
            }
            $shippingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('shipping'),
                'checkoutProvider',
                'shippingAddress',
                $shippingAddressFieldRowsLayout
            );
            $shippingAddressFieldRowsLayout = $this->rowsSorter->sort($shippingAddressFieldRowsLayout, 'shipping');
            $shippingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $shippingAddressFieldRowsLayout,
                'shipping'
            );
            $jsLayout = $this->arrayManager->set(
                $shippingAddressFieldRowsPath,
                $jsLayout,
                $shippingAddressFieldRowsLayout
            );
        }

        $billingAddressFieldRowsPath = 'components/checkout/children/paymentMethod/children/billingAddress/'
            . 'children/billing-address-fieldset/children';
        $billingAddressFieldRowsLayout = $this->arrayManager->get($billingAddressFieldRowsPath, $jsLayout);
        if ($billingAddressFieldRowsLayout) {
            if (!$this->config->isBusinessInformationEnabled()){
                unset($billingAddressFieldRowsLayout['business-field-row']);
            }
            $billingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('billing'),
                'checkoutProvider',
                'billingAddress',
                $billingAddressFieldRowsLayout
            );
            $billingAddressFieldRowsLayout = $this->rowsSorter->sort($billingAddressFieldRowsLayout, 'billing');
            $billingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $billingAddressFieldRowsLayout,
                'billing'
            );
            $jsLayout = $this->arrayManager->set(
                $billingAddressFieldRowsPath,
                $jsLayout,
                $billingAddressFieldRowsLayout
            );
        }

        return $jsLayout;
    }
}
