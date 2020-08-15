<?php

namespace Mageplaza\Smtp\Api\Data;

Interface QueueInterface {
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID                = 'id';
    const TEMPLATE_XML_PATH = 'template_xml_path';
    const ORDER_ID          = 'order_id';
    const STORE_ID          = 'store_id';
    const VARIABLES         = 'variables';
    const SENDER_INFO       = 'sender_info';
    const RECEIVER_INFO     = 'receiver_info';
    const CREATION_TIME     = 'creation_time';
    const SENT_AT           = 'sent_at';
    const EMAIL_SENT        = 'email_sent';
    const CC                = 'cc';
    const BCC               = 'bcc';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Template Xml Path
     *
     * @return string|null
     */
    public function getTemplateXmlPath();

    /**
     * Get Order ID
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get Variables
     *
     * @return string|null
     */

    public function getVariables();

    /**
     * Get Sender Info
     *
     * @return string|null
     */
    public function getSenderInfo();

    /**
     * Get Receiver Info
     *
     * @return string|null
     */
    public function getReceiverInfo();

    /**
     * Get Creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get Sent At
     *
     * @return string|null
     */
    public function getSentAt();

    /**
     * get Email Sent
     *
     * @return bool|null
     */
    public function getEmailSent();

    /**
     * Get CC
     *
     * @return string|null
     */
    public function getCc();

    /**
     * Get BCC
     *
     * @return string|null
     */
    public function getBcc();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setId($id);

    /**
     * Set Template Xml Path
     *
     * @param string $templateXmlPath
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setTemplateXmlPath($templateXmlPath);

    /**
     * Set Order ID
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setOrderId($id);

    /**
     * Set Store ID
     *
     * @param int $id
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setStoreId($id);

    /**
     * Set Variables
     *
     * @param string $variables
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setVariables($variables);

    /**
     * Set Sender Info
     *
     * @param string $senderInfo
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setSenderInfo($senderInfo);

    /**
     * Set Receiver Info
     *
     * @param string $receiverInfo
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setReceiverInfo($receiverInfo);

    /**
     * Set Creation time
     *
     * @param string $creationTime
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set Sent At
     *
     * @param string $sentAt
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setSentAt($sentAt);

    /**
     * Set email Sent
     *
     * @param int|bool $emailSent
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setEmailSent($emailSent);

    /**
     * Set CC
     *
     * @param string $cc
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setCc($cc);

    /**
     * Set BCC
     *
     * @param string $bcc
     * @return \Mageplaza\Smtp\Api\Data\QueueInterface
     */
    public function setBcc($bcc);
}