<?php

namespace Mageplaza\Smtp\Model;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\Smtp\Api\Data\QueueInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Queue extends AbstractModel implements QueueInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'mageplaza_smtp_queue';

    /**
     * @var string
     */
    protected $_cacheTag = 'mageplaza_smtp_queue';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Smtp\Model\ResourceModel\Queue');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get Template Xml Path
     *
     * @return string|null
     */
    public function getTemplateXmlPath()
    {
        return $this->getData(self::TEMPLATE_XML_PATH);
    }

    /**
     * Get Order Id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get Variables
     *
     * @return string|null
     */
    public function getVariables()
    {
        return $this->getData(self::VARIABLES);
    }

    /**
     * Get Sender Info
     *
     * @return string|null
     */
    public function getSenderInfo()
    {
        return $this->getData(self::SENDER_INFO);
    }

    /**
     * Get Receiver Info
     *
     * @return string|null
     */
    public function getReceiverInfo()
    {
        return $this->getData(self::RECEIVER_INFO);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get Sent At
     *
     * @return string|null
     */
    public function getSentAt()
    {
        return $this->getData(self::SENT_AT);
    }

    /**
     * Get Email Sent
     *
     * @return bool|null
     */
    public function getEmailSent()
    {
        return $this->getData(self::EMAIL_SENT);
    }

    /**
     * Get CC
     *
     * @return string|null
     */
    public function getCc()
    {
        return $this->getData(self::CC);
    }

    /**
     * Get Bcc
     *
     * @return string|null
     */
    public function getBcc()
    {
        return $this->getData(self::BCC);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set Template Xml Path
     *
     * @param string $templateXmlPath
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setTemplateXmlPath($templateXmlPath)
    {
        return $this->setData(self::TEMPLATE_XML_PATH, $templateXmlPath);
    }

    /**
     * Set Order Id
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setOrderId($id)
    {
        return $this->setData(self::ORDER_ID, $id);
    }

    /**
     * Set Store Id
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setStoreId($id)
    {
        return $this->setData(self::STORE_ID, $id);
    }

    /**
     * Set Variables
     *
     * @param string $variables
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setVariables($variables)
    {
        return $this->setData(self::VARIABLES, $variables);
    }

    /**
     * Set Sender Info
     *
     * @param string $senderInfo
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setSenderInfo($senderInfo)
    {
        return $this->setData(self::SENDER_INFO, $senderInfo);
    }

    /**
     * Set Receiver Info
     *
     * @param string $receiverInfo
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setReceiverInfo($receiverInfo)
    {
        return $this->setData(self::RECEIVER_INFO, $receiverInfo);
    }

    /**
     * Set Creation Time
     *
     * @param string $creationTime
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set Sent At
     *
     * @param string $sentAt
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setSentAt($sentAt)
    {
        return $this->setData(self::SENT_AT, $sentAt);
    }

    /**
     * Set Email Sent
     *
     * @param bool|int $emailSent
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setEmailSent($emailSent)
    {
        return $this->setData(self::EMAIL_SENT, $emailSent);
    }


    /**
     * Set CC
     *
     * @param string $cc
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setCc($cc)
    {
        return $this->setData(self::CC, $cc);
    }

    /**
     * Set BCC
     *
     * @param string $bcc
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setBcc($bcc)
    {
        return $this->setData(self::BCC, $bcc);
    }
}