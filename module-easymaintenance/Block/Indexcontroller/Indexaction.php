<?php

/**
 * Copyright © 2015 Biztech . All rights reserved.
 */

namespace Biztech\Easymaintenance\Block\Indexcontroller;

use Biztech\Easymaintenance\Block\BaseBlock;

class Indexaction extends BaseBlock {

    public function bc_getStoreId($id = null) {
        /** @var \Magento\Store\Model\StoreManagerInterface $manager */
        $manager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        return $manager->getStore($id);
    }

    public function getMediaDir() {
        $dir = $this->getObjectManager()->get('\Magento\Framework\App\Filesystem\DirectoryList');
        $base = $dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        return $base;
    }

    public function getCustomer() {
        $customerSession = $this->getObjectManager()->get('Magento\Customer\Model\Session');

        return $customerSession;
    }

    public function getFilterContent($content) {
        $filterContent = $this->getObjectManager()->get('Biztech\Easymaintenance\Helper\Processor');
        return $filterContent->content($content);
    }

    public function getRequireConfig() {
        $requirejsConfig = $this->getObjectManager()->get('\Magento\Framework\RequireJs\Config')->getConfigFileRelativePath();
        $basePath = $this->getObjectManager()->get('\Biztech\Easymaintenance\Model\Config')->getStoreManager()->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);

        return ($basePath . $requirejsConfig);
    }

    public function getBaseUrl() {
        return $basePath = $this->getObjectManager()->get('\Biztech\Easymaintenance\Model\Config')->getStoreManager()->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
    }

    public function getTimezone() {
        $timezone = $this->getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface')->getConfigTimezone();
        return ($timezone);
    }

    public function getCurrentdate($format, $timezone) {
        $current_date = $this->getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate($format, $timezone);
        return ($current_date);
    }

}
