<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Helper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Helper\AbstractHelper as CoreHelper;

class Data extends CoreHelper
{
	const XML_PATH_GENERAL_ENABLED = 'sociallogin/general/is_enabled';
	const XML_PATH_GENERAL = 'sociallogin/general/';
	const XML_PATH_GENERAL_POPUP_LEFT = 'sociallogin/general/left';
	const XML_PATH_GENERAL_STYLE_MANAGEMENT = 'sociallogin/general/style_management';
	const XML_PATH_CAPTCHA_ENABLE = 'sociallogin/captcha/is_enabled';
	const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';
	
	protected $_data = [];

	protected $storeManager;

	protected $objectManager;

	public function __construct(
		Context $context,
		ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager
	)
	{
		$this->objectManager = $objectManager;
		$this->storeManager  = $storeManager;

		parent::__construct($context);
	}
	
	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field,
			ScopeInterface::SCOPE_STORE,
			$storeId
		);
	}
	
	public function setData($name, $value)
	{
		$this->_data[$name] = $value;

		return $this;
	}

	public function getData($name)
	{
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		}

		return null;
	}

	public function getCurrentUrl()
	{
		$model = $this->objectManager->get('Magento\Framework\UrlInterface');

		return $model->getCurrentUrl();
	}

	public function createObject($path, $arguments = [])
	{
		return $this->objectManager->create($path, $arguments);
	}

	public function getObject($path)
	{
		return $this->objectManager->get($path);
	}
	
	public function isEnabled($storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_GENERAL_ENABLED, $storeId);
	}

	public function isCaptchaEnabled($storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_CAPTCHA_ENABLE, $storeId);
	}

	public function captchaResolve(\Magento\Framework\App\RequestInterface $request, $formId)
	{
		$captchaParams = $request->getPost(\Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE);

		return isset($captchaParams[$formId]) ? $captchaParams[$formId] : '';
	}

	public function getGeneralConfig($code, $storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_GENERAL . $code, $storeId);
	}

	public function canSendPassword()
	{
		return true;
	}

	public function getPopupEffect($storeId = null)
	{
		return $this->getGeneralConfig('popup_effect', $storeId);
	}

	public function isSecure()
	{
		$isSecure = $this->getConfigValue(self::XML_PATH_SECURE_IN_FRONTEND);

		return $isSecure;
	}
}