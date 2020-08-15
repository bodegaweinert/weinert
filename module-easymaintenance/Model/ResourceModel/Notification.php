<?php
/**
 * Copyright © 2015 Biztech. All rights reserved.
 */
namespace Biztech\Easymaintenance\Model\ResourceModel;

/**
 * Notification resource
 */
class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        // $this->_init('easymaintenance_initaction', 'id');
        $this->_init('easymaintenance_sitenotify', 'id');
    }

  
}
