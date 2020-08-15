<?php
namespace Aheadworks\OneStepCheckout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;


class CustomerAccountCreated  extends Template
{

    /**
     * {@inheritdoc}
     */
    protected $_template = 'customer_account_created.phtml';

    /**
     * {@inheritdoc}
     */
    protected $_isScopePrivate = true;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;


    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    public function isVisible(){
        $accountCreated = $this->checkoutSession->getData('dx_account_created',true);
        return $accountCreated;
    }
}