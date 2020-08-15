<?php

namespace Aheadworks\OneStepCheckout\Plugin\Customer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\CustomerRegistry;
use Mageplaza\Smtp\Helper\QueueHelper;
use Magento\Store\Model\ScopeInterface;

class EmailNotificationPlugin
{
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataProcessor;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var QueueHelper
     */
    protected $queueHelper;

    /**
     * @param CustomerRegistry $customerRegistry
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param DataObjectProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param QueueHelper $queueHelper
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        CustomerViewHelper $customerViewHelper,
        DataObjectProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        QueueHelper $queueHelper
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->customerViewHelper = $customerViewHelper;
        $this->dataProcessor = $dataProcessor;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->queueHelper = $queueHelper;
    }


    /**
     * @param \Magento\Customer\Model\EmailNotification $subject
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param string|int $storeId
     * @param string|null $sendemailStoreId
     *
     * @return mixed
     */
    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotification $subject,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $type = \Magento\Customer\Model\EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {

        if ($this->customerSession->getData('customer_password') == ''){
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }

        $templateXmlPath = 'customer/create_account/email_password_template';


        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
        }

        $emailVariables = array(
            'customer_firstname' => $customer->getFirstname(),
            'customer_lastname'  => $customer->getLastname(),
            'customer_email'     => $customer->getEmail(),
            'customer_name'      => $this->customerViewHelper->getCustomerName($customer),
            'customer_password'  =>  $this->customerSession->getData('customer_password',true),
            'back_url'           => $backUrl
        );

        $receiverInfo = [
            'name'  => $customer->getFirstname(),
            'email' => $customer->getEmail()
        ];


        /* Sender Detail  */
        $senderInfo = [
            'name'  => $this->_getConfigValue('trans_email/ident_general/name', $storeId),
            'email' => $this->_getConfigValue('trans_email/ident_general/email', $storeId)
        ];


        $this->queueHelper->saveToQueue($templateXmlPath,null, $storeId, $emailVariables, $senderInfo, $receiverInfo);

    }

    private function _getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    private function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }
}
