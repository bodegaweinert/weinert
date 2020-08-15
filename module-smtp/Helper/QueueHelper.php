<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 03/08/18
 * Time: 12:24
 */

namespace Mageplaza\Smtp\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Mageplaza\Smtp\Model\QueueFactory;

class QueueHelper extends AbstractHelper
{
    /** @var \Mageplaza\Smtp\Model\QueueFactory $queueFactory*/
    protected $queueFactory;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mageplaza\Smtp\Model\QueueFactory $queueFactory
     */

    public function __construct(
        Context $context,
        QueueFactory $queueFactory
    )
    {
        parent::__construct($context);
        $this->queueFactory = $queueFactory;
    }

    public function saveToQueue($templateXmlPath, $orderId, $storeId, $variables, $senderInfo, $receiverInfo, $cc = null, $bcc = null){
        try {
            $queueItem = $this->queueFactory->create();

            $queueItem->setTemplateXmlPath($templateXmlPath);
            $queueItem->setOrderId($orderId);
            $queueItem->setStoreId($storeId);
            $queueItem->setVariables(serialize($variables));
            $queueItem->setSenderInfo(serialize($senderInfo));
            $queueItem->setReceiverInfo(serialize($receiverInfo));

            $queueItem->setCc($cc);
            $queueItem->setBcc($bcc);

            $queueItem->save();

            return;
        } catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

}