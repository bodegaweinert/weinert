<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;

class Social extends AbstractModel
{
	protected $storeManager;

	protected $customerFactory;
	
	protected $apiHelper;

	protected $apiName;
	
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		CustomerFactory $customerFactory,
		StoreManagerInterface $storeManager,
		\Sm\SocialLogin\Helper\Social $apiHelper,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);

		$this->customerFactory = $customerFactory;
		$this->storeManager    = $storeManager;
		$this->apiHelper       = $apiHelper;
	}

	protected function _construct()
	{
		$this->_init('Sm\SocialLogin\Model\ResourceModel\Social');
	}

	public function getCustomerBySocial($identify, $type)
	{
		$customer = $this->customerFactory->create();

		$socialCustomer = $this->getCollection()
			->addFieldToFilter('social_id', $identify)
			->addFieldToFilter('type', $type)
			->getFirstItem();
		if ($socialCustomer && $socialCustomer->getId()) {
			$customer->load($socialCustomer->getCustomerId());
		}

		return $customer;
	}

	public function getCustomerByEmail($email, $websiteId = null)
	{
		$customer = $this->customerFactory->create();

		$customer->setWebsiteId($websiteId ?: $this->storeManager->getWebsite()->getId());
		$customer->loadByEmail($email);

		return $customer;
	}

	public function createCustomerSocial($data, $store)
	{
		$customer = $this->customerFactory->create();
		$customer->setFirstname($data['firstname'])
			->setLastname($data['lastname'])
			->setEmail($data['email'])
			->setStore($store);

		try {
			$customer->save();

			$this->setAuthorCustomer($data['identifier'], $customer->getId(), $data['type']);

			return $customer;
		} catch (\Exception $e) {
			if ($customer->getId()) {
				$customer->delete();
			}

			throw $e;
		}
	}

	public function setAuthorCustomer($identifier, $customerId, $type)
	{
		$this->setData([
			'social_id'              => $identifier,
			'customer_id'            => $customerId,
			'type'                   => $type,
			'is_send_password_email' => $this->apiHelper->canSendPassword()
		])
			->setId(null)
			->save();

		return $this;
	}

	public function getUserProfile($apiName)
	{
		$config = [
			"base_url"   => $this->apiHelper->getBaseAuthUrl(),
			"providers"  => [
				$apiName => $this->getProviderData($apiName)
			],
			"debug_mode" => false
		];

		try {
			$auth    = new \Hybrid_Auth($config);
			$adapter = $auth->authenticate($apiName, $this->apiHelper->getAuthenticateParams($apiName));
			return $adapter->getUserProfile();
		} catch (\Exception $e) {
			return '';
		}
	}

	public function getProviderData($apiName)
	{
		$data = [
			"enabled" => $this->apiHelper->isEnabled(),
			"keys"    => [
				'id'     => $this->apiHelper->getAppId(),
				'key'    => $this->apiHelper->getAppId(),
				'secret' => $this->apiHelper->getAppSecret()
			]
		];

		return array_merge($data, $this->apiHelper->getSocialConfig($apiName));
	}
}
