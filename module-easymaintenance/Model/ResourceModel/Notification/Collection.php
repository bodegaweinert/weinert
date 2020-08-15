<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Biztech\Easymaintenance\Model\ResourceModel\Notification;

/**
 * Notifications Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Biztech\Easymaintenance\Model\Notification', 'Biztech\Easymaintenance\Model\ResourceModel\Notification');
    }

    /**
     * checks if user is already registered for notification
     * @param string $email - email address of user
     * @return bool - user is registered
     *  
     **/
    public function isUserNotificationEnabled($email) {

    	$select = $this->getSelect()->reset('column')->where('email=?',$email);
    	
    	$result = $this->getConnection()->query($select)->fetchAll();
    	return (bool)count($result);
    }
}
