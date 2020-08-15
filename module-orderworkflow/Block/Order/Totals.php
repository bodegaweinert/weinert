<?php

namespace Combinatoria\OrderWorkflow\Block\Order;

class Totals extends \Magento\Sales\Block\Order\Totals
{

    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = [];
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            ['code' => 'subtotal', 'value' => $source->getSubtotal(), 'label' => __('Subtotal')]
        );

        /**
         * Add shipping
         */
        if (!$source->getIsVirtual() && ((double)$source->getShippingAmount() || $source->getShippingDescription())) {
            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'field' => 'shipping_amount',
                    'value' => $this->getSource()->getShippingAmount(),
                    'label' => __('Envío ('.$this->getSource()->getShippingDescription().')'),
                ]
            );
        }

        /**
         * Add discount
         */
        if ((double)$this->getSource()->getDiscountAmount() != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = __('Discount (%1)', $source->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
            $this->_totals['discount'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'discount',
                    'field' => 'discount_amount',
                    'value' => $source->getDiscountAmount(),
                    'label' => $discountLabel,
                ]
            );
        }

        /**
         * Add Finance Cost Amount
         **/

        if ((double)$this->getSource()->getFinanceCostAmount() != 0) {
            $this->_totals['finance_cost'] = new \Magento\Framework\DataObject(
                [
                    'code'  => 'finance_cost',
                    'field' => 'finance_cost_amount',
                    'value' => $this->getSource()->getFinanceCostAmount(),
                    'label' => __('Costos de financiación de Mercado Pago'),
                ]
            );
        }

        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(
            [
                'code' => 'grand_total',
                'field' => 'grand_total',
                'strong' => true,
                'value' => $source->getGrandTotal(),
                'label' => __('Grand Total'),
            ]
        );

        /**
         * Base grandtotal
         */
        if ($this->getOrder()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'base_grandtotal',
                    'value' => $this->getOrder()->formatBasePrice($source->getBaseGrandTotal()),
                    'label' => __('Grand Total to be Charged'),
                    'is_formated' => true,
                ]
            );
        }
        return $this;
    }

}