<?php
namespace Sm\Market\Block\Customer;


class Link extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
    }

    public function getCustomerName()
    {
        if($this->customerSession->getCustomer()->getFirstname()){
            return $this->customerSession->getCustomer()->getFirstname();
        }else{
            return __("My Account");
        }
    }
}