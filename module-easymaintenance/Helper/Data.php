<?php

/**
 * Copyright © 2015 Biztech . All rights reserved.
 */

namespace Biztech\Easymaintenance\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_ENABLED = 'easymaintenance/general/is_active';
    //const XML_API_KEY = 'carriers/auspost/auspost_api_key';
    const XML_PATH_INSTALLED = 'easymaintenance/activation/installed';
    const XML_PATH_DATA = 'easymaintenance/activation/data';
    const XML_PATH_WEBSITES = 'easymaintenance/activation/websites';
    const XML_PATH_EN = 'easymaintenance/activation/en';
    const XML_PATH_KEY = 'easymaintenance/activation/key';

    protected $_logger;
    protected $_moduleList;
    protected $_zend;
    protected $_resourceConfig;
    protected $_encryptor;
    protected $_web;
    protected $_objectManager;
    protected $_coreConfig;
    protected $_store;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Module\ModuleListInterface $moduleList, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Zend\Json\Json $zend, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Framework\ObjectManagerInterface $objectmanager, \Magento\Framework\App\Config\ReinitableConfigInterface $coreConfig, \Magento\Store\Model\Website $web, \Magento\Store\Model\StoreManagerInterface $store
    ) {
        $this->_zend = $zend;
        $this->_logger = $logger;
        $this->_moduleList = $moduleList;
        $this->_storeManager = $storeManager;
        $this->_resourceConfig = $resourceConfig;
        $this->_encryptor = $encryptor;
        $this->_web = $web;
        $this->_objectManager = $objectmanager;
        $this->_coreConfig = $coreConfig;
        $this->_store = $store;
        parent::__construct($context);
    }

    public function buildHttpQuery($query) {
        $query_array = array();
        foreach ($query as $key => $key_value)
            $query_array[] = $key . '=' . urlencode($key_value);
        return implode('&', $query_array);
    }

    public function parseXml($xmlString) {
        libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($xmlString);
        $result = array();
        if (!empty($xmlObject))
            $this->convertXmlObjToArr($xmlObject, $result);
        return $result;
    }

    public function convertXmlObjToArr($obj, &$arr) {
        $children = $obj->children();
        $executed = false;
        foreach ($children as $elementName => $node) {
            if (is_array($arr) && array_key_exists($elementName, $arr)) {
                if (is_array($arr[$elementName]) && array_key_exists(0, $arr[$elementName])) {
                    $i = count($arr[$elementName]);
                    $this->convertXmlObjToArr($node, $arr[$elementName][$i]);
                } else {
                    $tmp = $arr[$elementName];
                    $arr[$elementName] = array();
                    $arr[$elementName][0] = $tmp;
                    $i = count($arr[$elementName]);
                    $this->convertXmlObjToArr($node, $arr[$elementName][$i]);
                }
            } else {
                $arr[$elementName] = array();
                $this->convertXmlObjToArr($node, $arr[$elementName]);
            }
            $executed = true;
        }
        if (!$executed && $children->getName() == "") {
            $arr = (String) $obj;
        }
        return;
    }

    public function getAllStoreDomains() {
        $domains = array();
        foreach ($this->_storeManager->getWebsites() as $website) {
            $url = $website->getConfig('web/unsecure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
                $domains[] = $domain;
            }
            $url = $website->getConfig('web/secure/base_url');
            if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
                $domains[] = $domain;
            }
        }
        return array_unique($domains);
    }

    public function getDataInfo() {
        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return json_decode(base64_decode($this->_encryptor->decrypt($data)));
    }

    public function getAllWebsites() {
        $value = $this->scopeConfig->getValue(self::XML_PATH_INSTALLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$value) {
            return array();
        }
        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $web = $this->scopeConfig->getValue(self::XML_PATH_WEBSITES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //$websites = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = array_diff($websites, array(""));
        return $websites;
    }

    public function getFormatUrl($url) {
        $input = trim($url, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        if (isset($urlParts['path'])) {
            $domain = preg_replace('/^www\./', '', $urlParts['host'] . $urlParts['path']);
        } else {
            $domain = preg_replace('/^www\./', '', $urlParts['host']);
        }
        return $domain;
    }

    public function isEnabled() {
        $websiteId = $this->_store->getStore()->getWebsite()->getId();
        $isenabled = $this->scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isenabled) {
            if ($websiteId) {
                $websites = $this->getAllWebsites();
//                $key = $this->scopeConfig->getValue(self::XML_PATH_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//                if ($key == null || $key == '') {
//                    return false;
//                } else {
//                    $en = $data = $this->scopeConfig->getValue(self::XML_PATH_EN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//                    if ($isenabled && $en && in_array($websiteId, $websites)) {
//                        return true;
//                    } else {
//                        return false;
//                    }
//                }
		return true;
            } else {
                $en = $en = $data = $this->scopeConfig->getValue(self::XML_PATH_EN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($isenabled && $en) {
                    return true;
                }
            }
        }
    }

    /**
     * Returns expression of days passed from $startDate to $endDate
     *
     * @param  string|\Zend_Db_Expr $startDate
     * @param  string|\Zend_Db_Expr $endDate
     * @return \Zend_Db_Expr
     */
    public function getDateDiff($startDate, $endDate) {
        $dateDiff = '(TO_DAYS(' . $endDate . ') - TO_DAYS(' . $startDate . '))';
        return new \Zend_Db_Expr($dateDiff);
    }

}
