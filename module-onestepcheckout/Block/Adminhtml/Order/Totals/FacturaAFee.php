<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order\Totals;

use Magento\Sales\Model\Order;

class FacturaAFee extends \Magento\Framework\View\Element\Template
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
        if ((float)$this->getSource()->getRegEspImp() == 0) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject([
            'code'  => 'factura_a_fee',
            'field' => 'factura_a_fee',
            'value' => $this->getSource()->getRegEspImp(),
            'label' => __('Factura A Perc. IIBB'),
        ]);
        $this->getParentBlock()->addTotalBefore($total, 'shipping');

        return $this;
    }
}
