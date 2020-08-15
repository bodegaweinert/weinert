<?php

namespace Biztech\Easymaintenance\Controller\Indexcontroller;

class Postfeedback extends \Magento\Framework\App\Action\Action {

    const XML_PATH_EMAIL_RECIPIENT = 'easymaintenance/contactus/from_mail';
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_CONTACTS = 'contacts/email/recipient_email';

    protected $_inlineTranslation;
    protected $_storeConfig;
    protected $_escaper;

    function execute() {
        $post = $this->_request->getPost();

        if ($post) {
            try {
                $this->_storeConfig = $this->_objectManager->get('Biztech\Easymaintenance\Model\Config');
                $this->_inlineTranslation = $this->_objectManager->get('Magento\Framework\Translate\Inline\StateInterface');
                $this->_escaper = $this->_objectManager->get('Magento\Framework\Escaper');
                $this->_inlineTranslation->suspend();

                $this->_transportBuilder = $this->_objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');

                $postObject = new \Magento\Framework\DataObject();

                $post['feedbackdetails'] = nl2br($post['feedbackdetails']);
                if ($post['feedbackheard'] == null || $post['feedbackheard'] == '')
                    $post['feedbackheard'] = 'N/A';

                $postObject->addData($post->toArray());


                $error = false;
                $recipient = '';

                if ($this->_storeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT) == '') {
                    $recipient = $this->_storeConfig->getValue(self::XML_PATH_EMAIL_CONTACTS);
                } else {
                    $recipient = $this->_storeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT);
                }


                $sender = [
                    'name' => $this->_escaper->escapeHtml($post['feedbackbuname']),
                    'email' => $this->_escaper->escapeHtml($post['feedbackmail'])
                ];

                $transport = $this->_transportBuilder
                        ->setTemplateIdentifier('easymaintenance_feedback_template')
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeConfig->getStoreId()])
                        ->setTemplateVars(['store' => $this->_storeConfig->getStore(), 'data' => $postObject])
                        ->setFrom($sender)
                        ->addTo($recipient)
                        ->getTransport();

                $transport->sendMessage();

                /* End Change */
                $this->_inlineTranslation->resume();
                $var1 = array(
                    'result' => 'success',
                    'message' => __('Your Request Has Been Sent')
                );
                $resultData = json_encode($var1);
                $this->getResponse()->setBody($resultData);
                return;
            } catch (\Exception $e) {
                $var1['result'] = 'error';
                $message = $e->getMessage();
                if ($message == '') {
                    $var1['message'] = __('Unable to submit your request. Please try again Later');
                } else {
                    $var1['message'] = $message;
                }
                $resultData = json_encode($var1);
                $this->getResponse()->setBody($resultData);
                return;
            }
        } else {
            $var1['result'] = __('error');
            $var1['message'] = __('Unable to submit your request. Please, try again later');
            $resultData = json_encode($var1);
            $this->getResponse()->setBody($resultData);
            return;
        }
    }

}
