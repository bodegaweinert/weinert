<?php

namespace Biztech\Easymaintenance\Controller\Indexcontroller;

class Postnotify extends \Magento\Framework\App\Action\Action {

    function execute() {
        $post = $this->_request->getPost();
        $notifyname = $post['notifyuname'];
        $notifyemail = $post['notifymail'];

        if (isset($post) && $notifyname != '' && $notifyemail != '') {
            //Check if user with same email exists in database
            $userInNotifyList = $this->_objectManager->get('Biztech\Easymaintenance\Model\ResourceModel\Notification\Collection')->isUserNotificationEnabled($notifyemail);

            if ($userInNotifyList) {
                $var1 = array(
                    'result' => 'error',
                    'message' => __('An user with same email address has already registered for Notification.'),
                );
                $notifydata = json_encode($var1);
                $this->getResponse()->setBody($notifydata);
                return;
            } else {
                try {
                    $notification = $this->_objectManager->get('\Biztech\Easymaintenance\Model\Notification');
                    $notification
                            ->setName($notifyname)
                            ->setEmail($notifyemail)
                            ->save();

                    $var1 = array(
                        'result' => 'success',
                        'message' => __('You are registered successfully for notification')
                    );
                    $notifydata = json_encode($var1);
                    $this->getResponse()->setBody($notifydata);
                    return;
                } catch (\Exception $e) {
                    $var1 = array(
                        'result' => 'error'
                    );
                    $message = $e->getMessage();
                    if ($message == '') {
                        $var1['message'] = __('Unable to submit your request. Please, try again later');
                    } else {
                        $var1['message'] = $message;
                    }
                    $notifydata = json_encode($var1);
                    $this->getResponse()->setBody($notifydata);
                    return;
                }
            }
        } else {
            $var1 = array(
                'result' => 'error',
                'message' => __('Unable to submit your request. Please, try again later'),
            );
            $notifydata = json_encode($var1);
            $this->getResponse()->setBody($notifydata);

            return;
        }
    }

}
