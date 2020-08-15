<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Block\Popup;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sm\SocialLogin\Helper\Social as SocialHelper;

class Social extends Template
{
	protected $socialHelper;
	
	public function __construct(
		Context $context,
		SocialHelper $socialHelper,
		array $data = []
	)
	{
		$this->socialHelper = $socialHelper;

		parent::__construct($context, $data);
	}

	public function getAvailableSocials()
	{
		$availabelSocials = [];

		foreach ($this->socialHelper->getSocialTypes() as $socialKey => $socialLabel) {
			$this->socialHelper->setType($socialKey);
			if ($this->socialHelper->isEnabled()) {
				$availabelSocials[$socialKey] = [
					'label'     => $socialLabel,
					'login_url' => $this->getLoginUrl($socialKey),
				];
			}
		}

		return $availabelSocials;
	}

	public function getBtnKey($key)
	{
		return $key;
	}

	public function getSocialButtonsConfig()
	{
		$availableButtons = $this->getAvailableSocials();
		foreach ($availableButtons as $key => &$button) {
			$button['url']     = $this->getLoginUrl($key, ['authen' => 'popup']);
			$button['key']     = $key;
			$button['btn_key'] = $this->getBtnKey($key);
		}

		return $availableButtons;
	}

	public function canShow($position = null)
	{
		$displayConfig = '1,2,3,4';
		$displayConfig = explode(',', $displayConfig);

		if (!$position) {
			$position = ($this->getRequest()->getFullActionName() == 'customer_account_login') ?
				\Sm\SocialLogin\Model\System\Config\Source\Position::PAGE_LOGIN :
				\Sm\SocialLogin\Model\System\Config\Source\Position::PAGE_CREATE;
		}

		return in_array($position, $displayConfig);
	}

	public function getLoginUrl($socialKey, $params = [])
	{
		$params['type'] = $socialKey;

		return $this->getUrl('sociallogin/social/login', $params);
	}
}
