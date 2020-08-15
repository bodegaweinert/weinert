<?php
namespace Combinatoria\OrderWorkflow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;

Class Email extends AbstractHelper {

    const XML_PATH_ORDER_RECEIVED_EMAIL_TEMPLATE_FIELD    = 'ow_emails/order_received/order_received_template';
    const XML_PATH_ORDER_ACCREDITED_EMAIL_TEMPLATE_FIELD  = 'ow_emails/order_accredited/order_accredited_template';
    const XML_PATH_ORDER_DELIVERED_EMAIL_TEMPLATE_FIELD   = 'ow_emails/order_delivered/order_delivered_template';

    const EMAIL_TYPE_ORDER_RECEIVED   = 1;
    const EMAIL_TYPE_ORDER_ACCREDITED = 2;
    const EMAIL_TYPE_ORDER_DELIVERED  = 3;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }
    /**
     * [generateTemplate description]  with template file and tempaltes variables values
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    public function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return;
    }

    /**
     * CustomMailSendMethod
     * @param  Integer $emailType
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    /* your send mail method*/
    public function CustomMailSendMethod($emailType, $emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $templateXmlPath = '';

        switch ($emailType){
            case self::EMAIL_TYPE_ORDER_RECEIVED : {
                $templateXmlPath = self::XML_PATH_ORDER_RECEIVED_EMAIL_TEMPLATE_FIELD;
                break;
            }
            case self::EMAIL_TYPE_ORDER_ACCREDITED: {
                $templateXmlPath = self::XML_PATH_ORDER_ACCREDITED_EMAIL_TEMPLATE_FIELD;
                break;
            }
            case self::EMAIL_TYPE_ORDER_DELIVERED: {
                $templateXmlPath = self::XML_PATH_ORDER_DELIVERED_EMAIL_TEMPLATE_FIELD;
                break;
            }
        }

        if ($templateXmlPath == '') {
            return;
        }

        $this->temp_id = $this->getTemplateId($templateXmlPath);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        return;
    }
}