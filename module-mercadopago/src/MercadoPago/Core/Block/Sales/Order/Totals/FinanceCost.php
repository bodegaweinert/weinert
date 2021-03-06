<?php

namespace MercadoPago\Core\Block\Sales\Order\Totals;

use Magento\Sales\Model\Order;

/**
 * Class FinanceCost
 *
 * @package MercadoPago\Core\Block\Sales\Order\Totals
 */
class FinanceCost
    extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Add this total to parent
     */
    public function initTotals()
    {
        if ((float)$this->getSource()->getFinanceCostAmount() == 0) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject([
            'code'  => 'finance_cost',
            'field' => 'finance_cost_amount',
            'value' => $this->getSource()->getFinanceCostAmount(),
            'label' => __('Costos de financiación de Mercado Pago'),
        ]);
        $this->getParentBlock()->addTotalBefore($total, 'shipping');

        return $this;
    }
}
