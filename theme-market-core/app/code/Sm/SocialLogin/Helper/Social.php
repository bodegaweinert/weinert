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

use Sm\SocialLogin\Helper\Data as HelperData;

class Social extends HelperData
{
	protected $_type;

	public function setType($type)
	{
		$listTypes = $this->getSocialTypes();
		if (!$type || !array_key_exists($type, $listTypes)) {
			return null;
		}

		$this->_type = $type;

		return $listTypes[$type];
	}
	
	public function getSocialTypes()
	{
		return [
			'facebook'   => 'Facebook',
			'google'     => 'Google',
			'twitter'    => 'Twitter',
			'linkedin'   => 'LinkedIn',
			'yahoo'      => 'Yahoo',
			'instagram'  => 'Instagram',
		];
	}
	
	public function getSocialConfig($type)
	{
		$apiData = [
			'Facebook'  => ["trustForwarded" => false, 'scope' => 'email, public_profile'],
			'Twitter'   => ["includeEmail" => true],
			'LinkedIn'  => ["fields" => ['id', 'first-name', 'last-name', 'email-address']],
			'Instagram' => ['wrapper' => ['class' => '\Sm\SocialLogin\Model\Providers\Instagram']],
		];

		if ($type && array_key_exists($type, $apiData)) {
			return $apiData[$type];
		}

		return [];
	}

	public function getAuthenticateParams($type)
	{
		return null;
	}
	
	public function isEnabled($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/is_enabled", $storeId);
	}
	
	public function getAppId($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/app_id", $storeId);
	}
	
	public function getAppSecret($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/app_secret", $storeId);
	}
	
	public function getAuthUrl($type)
	{
		$authUrl = $this->getBaseAuthUrl();

		$type = $this->setType($type);
		switch ($type) {
			case 'Facebook':
				$param = 'hauth_done=' . $type;
				break;
			case 'Live':
				$param = null;
				break;
			default:
				$param = 'hauth.done=' . $type;
		}

		return $authUrl . ($param ? (strpos($authUrl, '?') ? '&' : '?') . $param : '');
	}

	public function getBaseAuthUrl()
	{
		return $this->_getUrl('sociallogin/social/callback', array('_nosid' => true, '_scope' => $this->getScopeUrl()));
	}
	
	protected function getScopeUrl()
	{
		$scope = $this->_request->getParam(\Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

		if ($website = $this->_request->getParam(\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)) {
			$scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
		}

		return $scope;
	}
}