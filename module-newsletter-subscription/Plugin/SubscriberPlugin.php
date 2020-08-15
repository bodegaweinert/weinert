<?php
namespace Combinatoria\NewsletterSubscription\Plugin;

use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\RuleRepository;
use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory;
use Magento\SalesRule\Model\Coupon\Massgenerator;

class SubscriberPlugin
{
    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * @var StateInterface $inlineTranslation
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder $transportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var RuleRepository $ruleRepository
     */
    protected $ruleRepository;

    /**
     * @var CollectionFactory $couponCollection
     */
    protected $couponCollection;

    /**
     * @var Massgenerator $massGenerator
     */
    protected $massGenerator;

    /**
     * Class Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param RuleRepository $ruleRepository
     * @param Massgenerator $massGenerator
     * @param CollectionFactory $couponCollection
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        RuleRepository $ruleRepository,
        Massgenerator $massGenerator,
        CollectionFactory $couponCollection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->ruleRepository = $ruleRepository;
        $this->massGenerator = $massGenerator;
        $this->couponCollection = $couponCollection;
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     *
     * @return mixed
     * @throws \Exception
     */
    public function aroundSendConfirmationSuccessEmail(
        Subscriber $subject,
        callable $proceed
    ){
        $couponCode = '';

        /*******************************/
        /* CREATES COUPON IF AVAILABLE */
        /*******************************/

        /* if origin is popup, then continue with the original func*/

        if ($subject->getOrigin() == 'popup'){
            return $proceed();
        }

        $couponId = $this->scopeConfig->getValue('newsletter/coupon/coupon_code',ScopeInterface::SCOPE_STORE);

        /* if there is no rule configured, then continue with the original func*/
        if ($couponId == 0){
            return $proceed();
        }

        $rule = $this->ruleRepository->getById($couponId);

        /* if there is no rule, or the rule is not active, then continue with the original func*/
        if (!$rule || !$rule->getruleId() || !$rule->getIsActive()) {
            return $proceed();
        }

        $codeLength =  $this->scopeConfig->getValue('newsletter/coupon/code_length',ScopeInterface::SCOPE_STORE);
        $codeFormat =  $this->scopeConfig->getValue('newsletter/coupon/code_format',ScopeInterface::SCOPE_STORE);
        $codeDash   =  $this->scopeConfig->getValue('newsletter/coupon/dash_every_x_characters',ScopeInterface::SCOPE_STORE);
        $codePrefix =  $this->scopeConfig->getValue('newsletter/coupon/code_prefix',ScopeInterface::SCOPE_STORE);
        $codeSuffix =  $this->scopeConfig->getValue('newsletter/coupon/code_suffix',ScopeInterface::SCOPE_STORE);

        if ($rule->getCouponType() == RuleInterface::COUPON_TYPE_SPECIFIC_COUPON) {
            if ($rule->getUseAutoGeneration()) {
                $data = [
                'rule_id'           => $rule->getRuleId(),

                'qty'               => 1,
                'length'            => $codeLength,
                'format'            => $codeFormat,
                'dash'              => $codeDash,
                'prefix'            => $codePrefix,
                'suffix'            => $codeSuffix,

                'uses_per_customer' => $rule->getUsesPerCustomer(),
                'uses_per_coupon'   => $rule->getUsesPerCoupon(),
                'to_date'           => $rule->getToDate()
                ];

                if ($this->massGenerator->validateData($data)) {
                    $couponCode = $this->massGenerator
                    ->setData($data)
                    ->generatePool()
                    ->getGeneratedCodes();

                    if (isset($couponCode[0])) {
                        $couponCode = $couponCode[0];
                    }
                }
            } else {
                /** @var $coupon \Magento\SalesRule\Model\Coupon*/
                $coupon = $this->couponCollection->create()->addFieldToFilter('rule_id', ['eq' => $rule->getRuleId()])->getFirstItem();
                if ($coupon->getCouponId()) {
                    $couponCode = $coupon->getCode();
                }
            }
        } else {
            return $proceed();
        }

        /**********************/
        /* SEND SUCCESS EMAIL */
        /**********************/

        if ($subject->getImportMode()) {
            return $subject;
        }

        if (!$this->scopeConfig->getValue(Subscriber::XML_PATH_SUCCESS_EMAIL_TEMPLATE,ScopeInterface::SCOPE_STORE)
        || !$this->scopeConfig->getValue(Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY,ScopeInterface::SCOPE_STORE)
        ) {
            return $subject;
        }

        $this->inlineTranslation->suspend();

        $this->transportBuilder->setTemplateIdentifier(
            $this->scopeConfig->getValue(Subscriber::XML_PATH_SUCCESS_EMAIL_TEMPLATE,ScopeInterface::SCOPE_STORE)
        )->setTemplateOptions(
            [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['subscriber' => $subject, 'code'=> $couponCode]
        )->setFrom(
            $this->scopeConfig->getValue(Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY,ScopeInterface::SCOPE_STORE)
        )->addTo(
            $subject->getEmail(),
            $subject->getName()
        );
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        /**********************************/
        /* SAVE COUPON CODE TO SUBSCRIBER */
        /**********************************/

        $subject->loadByEmail($subject->getEmail());
        $subject->setCouponCode($couponCode);
        $subject->save();


        return $subject;
    }
}