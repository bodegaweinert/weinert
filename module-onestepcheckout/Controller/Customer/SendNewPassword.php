<?php

namespace Aheadworks\OneStepCheckout\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\OneStepCheckout\Helper\CustomerPassword;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

class SendNewPassword extends Action
{

    /**
     * @var CustomerPassword
     */
    protected $helper;

    /**
     * @param Context $context
     * @param CustomerPassword $helper
     */
    public function __construct(
    Context $context,
    CustomerPassword $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [];
        $customerEmail = $this->getRequest()->getParam('customer');

        try{
            $this->helper->changePassword($customerEmail);
            $resultData = [
                'success' => true,
                'errorMessage' => ''
            ];

        }catch (\Exception $exception){
            $resultData = [
                'success' => false,
                'errorMessage' => $exception->getMessage()
            ];
        }

        return $resultJson->setData($resultData);
    }

}