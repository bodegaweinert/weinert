<?php
namespace Aheadworks\OneStepCheckout\Helper;

use Braintree\Exception;
use Magento\Framework\Math\Random;
use Magento\Framework\Encryption\Encryptor;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CustomerPassword extends \Magento\Framework\App\Helper\AbstractHelper{

    const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_DIGITS = '0123456789';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
    const TEMPLATE_XML_PATH = 'customer/send_new_password/email_send_new_password_template';

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Encryptor $encryptor
     */
    protected $encryptor;

    /**
     * @var Random $mathRandom
     */
    protected $mathRandom;

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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param Random $mathRandom
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param DataObjectProcessor $dataProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Encryptor $encryptor,
        Random $mathRandom,
        TransportBuilder $transportBuilder,
        CustomerViewHelper $customerViewHelper,
        DataObjectProcessor $dataProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
        $this->mathRandom = $mathRandom;
        $this->transportBuilder = $transportBuilder;
        $this->customerViewHelper = $customerViewHelper;
        $this->dataProcessor = $dataProcessor;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }


    public function changePassword($customerEmail)
    {
        try{
            /**@var \Magento\Customer\Model\Customer $customer*/
            $customer = $this->customerRepository->get($customerEmail);

            do {
                $password = $this->mathRandom->getRandomString(8);
            } while (!$this->validatePassword($password));

            //$password = 'cmb_1234';

            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
            $this->customerRepository->save($customer);

            $storeId = 0;
            $sendemailStoreId = null;
            $backUrl = '';

            if (!$storeId) {
                $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
            }

            $store = $this->storeManager->getStore($customer->getStoreId());

            $customerEmailData = $this->getFullCustomerObject($customer, $password);

            $this->sendEmailTemplate(
                $customer,
                self::TEMPLATE_XML_PATH,
                self::XML_PATH_REGISTER_EMAIL_IDENTITY,
                ['customer' => $customerEmailData, 'back_url' => $backUrl, 'store' => $store],
                $storeId
            );

        } catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    private function validatePassword ($password){
        $charsLowersValidated = false;
        $charsUppersValidated = false;
        $charsDigitsValidated = false;

        for ($i = 0; $i < strlen($password); $i++){
            $char = $password[$i];
            if (strpos(self::CHARS_LOWERS,$char) !== false){
                $charsLowersValidated = true;
            }
            if (strpos(self::CHARS_UPPERS,$char) !== false){
                $charsUppersValidated = true;
            }
            if (strpos(self::CHARS_DIGITS,$char) !== false){
                $charsDigitsValidated = true;
            }
        }

        return ($charsLowersValidated && $charsUppersValidated && $charsDigitsValidated);
    }

    /**
     * Send corresponding email template
     *
     * @param CustomerInterface $customer
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $email
     * @return void
     */
    private function sendEmailTemplate(
        $customer,
        $template,
        $sender,
        $templateParams = [],
        $storeId = null,
        $email = null
    ) {
        $templateId = $this->scopeConfig->getValue($template, 'store', $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($this->scopeConfig->getValue($sender, 'store', $storeId))
            ->addTo($email, $this->customerViewHelper->getCustomerName($customer))
            ->getTransport();

        $transport->sendMessage();
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param CustomerInterface $customer
     * @param String $password
     * @return \Magento\Customer\Model\Data\CustomerSecure
     */
    private function getFullCustomerObject($customer, $password)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        $mergedCustomerData->setData('password', $password);
        return $mergedCustomerData;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }


}