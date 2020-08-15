<?php
namespace Mageplaza\Smtp\Cron;

use Mageplaza\Smtp\Model\QueueFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

class ProcessQueue
{
    private $storeID;


    /** @var \Mageplaza\Smtp\Model\QueueFactory $queueFactory*/
    protected $queueFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig*/
    protected $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager*/
    protected $storeManager;

    /** @var \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation*/
    protected $inlineTranslation;

    /** @var \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder*/
    protected $transportBuilder;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
    protected $orderRepository;

    /** @var \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository */
    protected $shipmentRepository;

    /**
     * Constructor
     * @param \Mageplaza\Smtp\Model\QueueFactory $queueFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        QueueFactory $queueFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository
    )
    {
        $this->queueFactory  = $queueFactory;
        $this->scopeConfig   = $scopeConfig;
        $this->storeManager  = $storeManager;
        $this->inlineTranslation  = $inlineTranslation;
        $this->transportBuilder   = $transportBuilder;
        $this->orderRepository    = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function execute()
    {
        $emailsToSend = $this->queueFactory->create()->getCollection()->addFieldToFilter('email_sent', 0);
        foreach ($emailsToSend as $emailToSend){
            $templateXmlPath = $emailToSend->getTemplateXmlPath();
            $orderID = $emailToSend->getOrderId();
            $this->storeID = $emailToSend->getStoreId();
            $emailVariables = unserialize($emailToSend->getVariables());
            $senderInfo = unserialize($emailToSend->getSenderInfo());
            $receiverInfo = unserialize($emailToSend->getReceiverInfo());

            $cc = $emailToSend->getCc();
            $cc_copy = null;
            $bcc = $emailToSend->getBcc();
            $bcc_copy = null;

            if ($cc != null && $cc != '') {
                $cc_copy  = explode(",",$emailToSend->getCc());
            }
            if ($bcc != null && $bcc != ''){
                $bcc_copy = explode(",",$emailToSend->getBcc());
            }


            $store = $this->storeManager->getStore($this->storeID);
            $emailVariables['store'] = $store;

            if ($orderID){
                $order = $this->orderRepository->get($orderID);
                $emailVariables['order'] = $order;

                $shipmentCollection = $order->getShipmentsCollection();
                $shipmentId = 0;
                foreach ($shipmentCollection as $shipment) {
                    $shipmentId = $shipment->getId();
                }
                if ($shipmentId != 0){
                    $shipment = $this->shipmentRepository->get($shipmentId);
                    $emailVariables['shipment'] = $shipment;
                }

            }

            $this->temp_id = $this->_getTemplateId($templateXmlPath);
            $this->inlineTranslation->suspend();
            $this->_generateTemplate($emailVariables,$senderInfo,$receiverInfo);
            $transport = $this->transportBuilder->getTransport();

            if ($cc_copy != null){
                $transport->getMessage()->addCc($cc_copy);
            }
            if ($bcc_copy != null){
                $transport->getMessage()->addBcc($bcc_copy);
            }

            $transport->sendMessage();
            $this->inlineTranslation->resume();

            $emailToSend->setEmailSent(1);
            $emailToSend->save();
        }
    }

    private function _getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    private function _getTemplateId($xmlPath)
    {
        return $this->_getConfigValue($xmlPath, $this->storeID);
    }

    private function _generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $template =  $this->transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeID,
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return;
    }
}