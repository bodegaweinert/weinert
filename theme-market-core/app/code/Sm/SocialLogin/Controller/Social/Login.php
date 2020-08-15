<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Controller\Social;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Store\Model\StoreManagerInterface;
use Sm\SocialLogin\Helper\Social as SocialHelper;
use Sm\SocialLogin\Model\Social;
use Magento\Customer\Model\Session;

class Login extends Action
{
	protected $session;

	protected $storeManager;

	protected $apiHelper;

	protected $apiObject;

	private $accountRedirect;
	
	private $cookieMetadataManager;
	
	private $cookieMetadataFactory;

	protected $resultRawFactory;

	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		SocialHelper $apiHelper,
		Social $apiObject,
		Session $customerSession,
		AccountRedirect $accountRedirect,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	)
	{
		parent::__construct($context);
		$this->storeManager     = $storeManager;
		$this->apiHelper        = $apiHelper;
		$this->apiObject        = $apiObject;
		$this->session          = $customerSession;
		$this->accountRedirect  = $accountRedirect;
		$this->urlBuilder       = $context->getUrl();
		$this->resultRawFactory = $resultRawFactory;
	}

	public function execute()
	{
		$type = $this->apiHelper->setType($this->getRequest()->getParam('type', null));
		
		if (!$type) {
			$this->_forward('noroute');

			return;
		}
		$userProfile = $this->apiObject->getUserProfile($type);
		if (empty($userProfile)) {
			$this->_redirect('customer/account/login');
			return;
		}
		if (!$userProfile->identifier) {
			return $this->emailRedirect($type);
		}

		$customer = $this->apiObject->getCustomerBySocial($userProfile->identifier, $type);
		if (!$customer->getId()) {
			$name = explode(' ', $userProfile->displayName ?: __('New User'));
			$user = array_merge([
				'email'      => $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com',
				'firstname'  => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
				'lastname'   => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
				'identifier' => $userProfile->identifier,
				'type'       => $type
			], $this->getUserData($userProfile));

			$customer = $this->createCustomer($user, $type);
		}

		return $this->_appendJs($customer);
	}

	protected function getUserData($profile)
	{
		return [];
	}

	public function getStore()
	{
		return $this->storeManager->getStore();
	}

	public function emailRedirect($apiLabel, $needTranslate = true)
	{
		$message = $needTranslate ? __('Email is Null, Please enter email in your %1 profile', $apiLabel) : $apiLabel;
		$this->messageManager->addErrorMessage($message);
		$this->_redirect('customer/account/login');

		return $this;
	}

	public function createCustomer($user, $type)
	{
		$customer = $this->apiObject->getCustomerByEmail($user['email'], $this->getStore()->getWebsiteId());
		if (!$customer->getId()) {
			try {
				$customer = $this->apiObject->createCustomerSocial($user, $this->getStore());
				if ($this->apiHelper->canSendPassword()) {
					$customer->sendPasswordReminderEmail();
				}
			} catch (\Exception $e) {
				$this->emailRedirect($e->getMessage(), false);

				return false;
			}
		}
		$this->apiObject->setAuthorCustomer($user['identifier'], $customer->getId(), $type);

		return $customer;
	}

	public function _appendJs($customer)
	{
		if ($customer && $customer->getId()) {
			$this->session->setCustomerAsLoggedIn($customer);
			$this->session->regenerateId();

			if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
				$metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
				$metadata->setPath('/');
				$this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
			}
		}

		$resultRaw = $this->resultRawFactory->create();
		return $resultRaw->setContents(sprintf("<script>window.opener.socialLoginCallback('%s', window);</script>", $this->_loginPostRedirect()));
	}

	private function getCookieManager()
	{
		if (!$this->cookieMetadataManager) {
			$this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
				\Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
			);
		}

		return $this->cookieMetadataManager;
	}

	private function getCookieMetadataFactory()
	{
		if (!$this->cookieMetadataFactory) {
			$this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
				\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
			);
		}

		return $this->cookieMetadataFactory;
	}

	protected function _loginPostRedirect()
	{
		$url = $this->urlBuilder->getUrl('customer/account');

		if ($this->_request->getParam('authen') == 'popup') {
			$url = $this->urlBuilder->getUrl('checkout');
		} else {
			$requestedRedirect = $this->accountRedirect->getRedirectCookie();
			if (!$this->apiHelper->getConfigValue('customer/startup/redirect_dashboard') && $requestedRedirect) {
				$url = $this->_redirect->success($requestedRedirect);
				$this->accountRedirect->clearRedirectCookie();
			}
		}

		return $url;
	}
}