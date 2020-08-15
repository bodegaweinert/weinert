<?php

namespace Biztech\Easymaintenance\Controller\Adminhtml\Notificationcontroller;

use Magento\Backend\App\Action;

class MassNotify extends Action {

    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_RECIPIENT = 'easymaintenance/contactus/from_mail';
    const XML_PATH_EMAIL_CONTACTS = 'contacts/email/recipient_email';

    protected $_storeConfig;
    protected $_transportBuilder;

    public function execute() {
        $notificationIds = $this->getRequest()->getParam('id');
        if (!is_array($notificationIds) || empty($notificationIds)) {
            $this->messageManager->addError(__('Please select items(s).'));
        } else {
            try {
                $this->_inlineTranslation = $this->_objectManager->get('Magento\Framework\Translate\Inline\StateInterface');
                $this->_inlineTranslation->suspend();
                $this->_storeConfig = $this->_objectManager->get('Biztech\Easymaintenance\Model\Config');
                $this->_transportBuilder = $this->_objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');

                $recipient = $this->_storeConfig->getValue(self::XML_PATH_EMAIL_SENDER);

                $notificationModel = $this->_objectManager->get('Biztech\Easymaintenance\Model\Notification');

                foreach ($notificationIds as $notificationId) {
                    $notification = $notificationModel->load($notificationId);
                    $to[] = $notification->getEmail();
                }

                $email = "developer1.test@gmail.com";
                $postObject = new \Magento\Framework\DataObject();
                $post = array();
                $postObject->addData($post);

                $error = false;

                $mail = $this->_transportBuilder->setTemplateIdentifier('easymaintenance_notification_template')
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeConfig->getStoreId()])
                        ->setTemplateVars([
                            'store' => $this->_storeConfig->getStore(),
                            'store_info' => $this->_storeConfig->getValue('general/store_information/name'),
                            'template' => $this->_storeConfig->getValue('easymaintenance/notify/template'),
                            'data' => $postObject
                        ])
                        ->setReplyTo($recipient)
                        ->addTo($recipient)
                        ->addBcc($to)
                        ->getTransport();
                $mail->sendMessage();

                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess(__(sprintf('Notification Sent Successfully to %d user(s)', count($notificationIds))));
            } catch (\Exception $e) {
                if ($e->getMessage() != '') {
                    $this->messageManager->addError($e->getMessage());
                } else {
                    $this->messageManager->addError(__('Notification not Sent, Please try Again Later'));
                }
            }
        }
        $this->_redirect('*/*/');
    }

}
