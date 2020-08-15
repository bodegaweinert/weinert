<?php

/**
 * Copyright © 2015 Biztech . All rights reserved.
 */

namespace Biztech\Easymaintenance\Block;

use Magento\Framework\UrlFactory;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class BaseBlock extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Biztech\Easymaintenance\Helper\Data
     */
    protected $_devToolHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlApp;

    /**
     * @var \Biztech\Easymaintenance\Model\Config
     */
    protected $_config;
    protected $_objectManager;
    protected $_assetRepo;
    protected $_themeProvider;
    protected $_storeManager;

    /**
     * @param \Biztech\Easymaintenance\Block\Context $context
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(
        \Biztech\Easymaintenance\Block\Context $context,
        ThemeProviderInterface $themeProvider,
            \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_devToolHelper = $context->getEasymaintenanceHelper();
        $this->_objectManager = $context->getObjectManager();
        $this->_config = $context->getConfig();
        $this->_urlApp = $context->getUrlFactory()->create();
        $this->_assetRepo = $context->getAssetRepository();
        $this->_themeProvider = $themeProvider;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    public function getMediaUrl()
    {
        return $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    public function getThemeData()
    {
        $themeId = $this->_config->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        if(!$themeId)
        {$themeId=1;}

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->_themeProvider->getThemeById($themeId);

        return $theme->getData();
    }

    public function getAssetsRepo()
    {
        return $this->_assetRepo;
    }

    /**
     * Function for getting event details
     * @return array
     */
    public function getEventDetails() {
        return $this->_devToolHelper->getEventDetails();
    }

    public function getObjectManager() {
        return $this->_objectManager;
    }

    /**
     * Function for getting current url
     * @return string
     */
    public function getCurrentUrl() {
        return $this->_urlApp->getCurrentUrl();
    }

    /**
     * Function for getting controller url for given router path
     * @param string $routePath
     * @return string
     */
    public function getControllerUrl($routePath) {

        return $this->_urlApp->getUrl($routePath);
    }

    /**
     * Function for getting current url
     * @param string $path
     * @return string
     */
    public function getConfigValue($path) {
        return $this->_config->getCurrentStoreConfigValue($path);
    }

    /**
     * Function canShowEasymaintenance
     * @return bool
     */
    public function canShowEasymaintenance() {
        $isEnabled = $this->getConfigValue('easymaintenance/general/is_enabled');
        if ($isEnabled) {
            $allowedIps = $this->getConfigValue('easymaintenance/general/allowed_ip');
            if (is_null($allowedIps)) {
                return true;
            } else {
                $remoteIp = $_SERVER['REMOTE_ADDR'];
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

}
