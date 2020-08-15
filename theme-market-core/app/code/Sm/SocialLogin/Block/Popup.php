<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */

namespace Sm\SocialLogin\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sm\SocialLogin\Helper\Data as HelperData;

class Popup extends Template
{
	protected $storeManager;

	protected $helperData;
	
	protected $customerSession;
	
	public function __construct(
		Context $context,
		HelperData $helperData,
		CustomerSession $customerSession,
		array $data = []
	)
	{
		$this->helperData      = $helperData;
		$this->customerSession = $customerSession;
		$this->storeManager    = $context->getStoreManager();
		parent::__construct($context, $data);
	}

	public function isEnabled()
	{
		return $this->helperData->isEnabled() && !$this->customerSession->isLoggedIn() && $this->helperData->getGeneralConfig('popup_login');
	}

	public function getFormParams()
	{
		$params = [
			'headerLink'    => $this->getHeaderLink(),
			'popupEffect'   => $this->getPopupEffect(),
			'formLoginUrl'  => $this->getFormLoginUrl(),
			'forgotFormUrl' => $this->getForgotFormUrl(),
			'createFormUrl' => $this->getCreateFormUrl()
		];

		return json_encode($params);
	}

	public function getHeaderLink()
	{
		$links = $this->helperData->getGeneralConfig('link_trigger');

		return $links ?: '.header .links, .section-item-content .header.links';
	}

	public function getPopupEffect()
	{
		return $this->helperData->getPopupEffect();
	}

	public function getFormLoginUrl()
	{
		return $this->getUrl('customer/ajax/login', ['_secure' => $this->isSecure()]);
	}

	public function isSecure()
	{
		return $this->helperData->isSecure();
	}

	public function getForgotFormUrl()
	{
		return $this->getUrl('sociallogin/popup/forgot', ['_secure' => $this->isSecure()]);
	}

	public function getCreateFormUrl()
	{
		return $this->getUrl('sociallogin/popup/create', ['_secure' => $this->isSecure()]);
	}
}
