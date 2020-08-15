<?php
namespace Aheadworks\OneStepCheckout\Model\Quote;

use Aheadworks\OneStepCheckout\Helper\Convenio as ConvenioHelper;
/**
 * Class FacturaAFee
 *
 * @package Aheadworks\OneStepCheckout\Model\Quote
 */
class FacturaAFee
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    protected $convenioHelper;
    /**
     * FinanceCost constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        ConvenioHelper $convenioHelper
    )
    {
        $this->setCode('factura_a_fee');
        $this->_registry = $registry;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->convenioHelper = $convenioHelper;
    }


    /**
     * Collect address discount amount
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        $address = $shippingAssignment->getShipping()->getAddress();

        if ($quote->getShippingAddress()->getRequireInvoice()) {
            parent::collect($quote, $shippingAssignment, $total);

            $subtotal = ( $quote->getSubtotalWithDiscount() + $quote->getShippingAmount() ) / 1.21;

            $amount_percentage = $this->convenioHelper->getTaxPercentage($quote->getShippingAddress()->getConvenio());
            $amount = number_format($subtotal * $amount_percentage,2);

            $address->setFacturaAFeeAmount($amount);
            $address->setBaseFacturaAFeeAmount($amount);

            $total->setFacturaAFeeDescription($this->getCode());
            $total->setFacturaAFeeAmount($amount);
            $total->setBaseFacturaAFeeAmount($amount);
        }

        $total->addTotalAmount($this->getCode(), $address->getFacturaAFeeAmount());
        $total->addBaseTotalAmount($this->getCode(), $address->getBaseFacturaAFeeAmount());

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        if ($quote->getShippingAddress()->getRequireInvoice()) {
            $subtotal = ( $quote->getSubtotalWithDiscount() + $quote->getShippingAmount() ) / 1.21;
            $amount_percentage = $this->convenioHelper->getTaxPercentage($quote->getShippingAddress()->getConvenio());
            $amount = number_format($subtotal * $amount_percentage,2);
        }else{
            $amount = 0;
        }

        $result = [
            'code'  => $this->getCode(),
            'title' => __('Factura A Perc. IIBB'),
            'value' => $amount
        ];

        return $result;
    }
}
