<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Model;

use Anowave\Ec\vendor\Google\Client as Google_Client;
use Anowave\Ec\vendor\Google\Service\TagManager as Google_Service_TagManager;
use Anowave\Ec\vendor\Google\Service\TagManager\Variable as Google_Service_TagManager_Variable;
use Anowave\Ec\vendor\Google\Service\TagManager\Trigger as Google_Service_TagManager_Trigger;
use Anowave\Ec\vendor\Google\Service\TagManager\Tag as Google_Service_TagManager_Tag;

class Api
{
	const TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE 		= 'v';
	const TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE 		= 'c';
	const TAG_MANAGER_VARIABLE_TYPE_JAVASCRIPT 				= 'jsm';
	const TAG_MANAGER_VARIABLE_TYPE_GA_SETTINGS 			= 'gas';
	
	/**
	 * Custom event trigger type
	 * 
	 * @var string
	 */
	const TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT 			= 'customEvent';
	
	
	/**
	 * Tag types
	 */
	const TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS 			= 'ua';
	const TAG_MANAGER_TAG_TYPE_SOCIAL			 			= 'social';
	const TAG_MANAGER_TAG_TYPE_ADWORDS_DYNAMIC_REMARKETING  = 'sp';
	
	/**
	 * All pages trigger 
	 * 
	 * @var integer
	 */
	const TRIGGER_ALL_PAGES = 2147479553;
	
	/*
	 * Trigger names
	 */
	const TRIGGER_ADD_TO_CART 								= 'Event Equals Add To Cart';
	const TRIGGER_REMOVE_FROM_CART 							= 'Event Equals Remove From Cart';
	const TRIGGER_CHECKOUT 									= 'Event Equals Checkout';
	const TRIGGER_CHECKOUT_OPTION 							= 'Event Equals Checkout Option';
	const TRIGGER_PRODUCT_CLICK								= 'Event Equals Product Click';
	const TRIGGER_REMARKETING_TAG							= 'Event Equals Dynamic Remarketing';
	const TRIGGER_SOCIAL_INTERACTION 						= 'Event Equals Social Interaction';
	const TRIGGER_USER_TIMING								= 'Event Equals User Timing';
	const TRIGGER_PURCHASE									= 'Event Equals Purchase';
	const TRIGGER_IMPRESSION								= 'Event Equals Impression';
	const TRIGGER_PROMOTION_CLICK							= 'Event Equals Promotion Click';
	const TRIGGER_PROMOTION_VIEW							= 'Event Equals Promotion View Non Interactive';
	const TRIGGER_NEW_WIDGET_VIEW							= 'Event Equals NewProducts View Non Interactive';
	const TRIGGER_DETAIL									= 'Event Equals Detail';
	const TRIGGER_ADD_WISHLIST								= 'Event Equals Wishlist';
	const TRIGGER_ADD_COMPARE								= 'Event Equals Compare';
	const TRIGGER_COOKIE_CONSENT_GRANTED					= 'Event Equals Cookie Consent Granted';
	
	/**
	 * Tag names
	 */
	const TAG_UA											= 'Universal Analytics';
	const TAG_ADD_TO_CART									= 'EE Add To Cart';
	const TAG_REMOVE_FROM_CART								= 'EE Remove From Cart';
	const TAG_CHECKOUT										= 'EE Checkout Step';
	const TAG_CHECKOUT_OPTION								= 'EE Checkout Step Option';
	const TAG_PRODUCT_CLICK									= 'EE Product Click';
	const TAG_SOCIAL_INTERACTION							= 'EE Social Interaction';
	const TAG_ADWORDS_DYNAMIC_REMARKETING					= 'EE AdWords Dynamic Remarketing';
	const TAG_USER_TIMING									= 'EE User Timing';
	const TAG_PURCHASE										= 'EE Async Purchase';
	const TAG_IMPRESSION									= 'EE Async Impression';
	const TAG_DETAIL										= 'EE Async Detail';
	const TAG_PROMOTION_CLICK								= 'EE Promotion Click';
	const TAG_PROMOTION_VIEW								= 'EE Promotion View';
	const TAG_NEW_WIDGET_VIEW								= 'EE NewProducts View';
	const TAG_ADD_TO_WISHLIST								= 'EE Add To Wishlist';
	const TAG_ADD_TO_COMPARE								= 'EE Add To Compare';
	
	/**
	 * Variable names
	 */
	const VARIABLE_GA										= 'Google Analytics Settings';
	
	/**
	 * @var Google_Service_TagManager
	 */
	private $service = null;
	
	/**
	 * OAuth2
	 *
	 * @var Google_Service_Oauth2
	 */
	private $oauth = null;
	
	/**
	 * OAuth Scopes
	 *
	 * @var array
	 */
	private $client = null;
	
	/**
	 * Access scopes 
	 * 
	 * @var []
	 */
	private $scopes = array
	(
		'https://www.googleapis.com/auth/userinfo.profile',
		'https://www.googleapis.com/auth/tagmanager.readonly',
		'https://www.googleapis.com/auth/tagmanager.edit.containers',
		'https://www.googleapis.com/auth/tagmanager.edit.containerversions',
		'https://www.googleapis.com/auth/tagmanager.publish',
		'https://www.googleapis.com/auth/tagmanager.manage.accounts'
	);
	
	private $containers = [];
	
	/**
	 * @var \Magento\Backend\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session;
	
	/**
	 * @var \Anowave\Ec\Helper\Scope
	 */
	protected $scope;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Helper\Data $helper
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Anowave\Ec\Helper\Scope $scope
	 */
	public function __construct
	(
		\Magento\Backend\Helper\Data $helper,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Anowave\Ec\Helper\Scope $scope
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Magento\Backend\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set session 
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;

		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Magento\Framework\Message\ManagerInterface $messageManager
		 */
		$this->messageManager = $messageManager;
		
		/**
		 * Set scope 
		 * 
		 * @var \Anowave\Ec\Helper\Scope $scope
		 */
		$this->scope = $scope;
	}
	
	/**
	 * Get Client
	 *
	 * @return Google_Client
	 */
	public function getClient()
	{
		if (!$this->client)
		{
			$this->client = new Google_Client();
	
			/**
			 * Set Application name
			*/
			$this->client->setApplicationName('Anowave');
			$this->client->setClientId('606603072955-f1aa5hg4sju2gsaj70voq6v0errut3gf.apps.googleusercontent.com');
			$this->client->setClientSecret('STtsqOh53TDXUMEGFlszhadf');
	
			/**
			 * Set scopes
			*/
			$this->client->setScopes($this->scopes);
	
			$this->client->setState
			(
				$this->helper->getUrl("adminhtml/system_config/edit", array('section' => 'ec'))
			);
	
			/**
			 * Set redirect URI
			*/
			$this->client->setRedirectUri('http://oauth.anowave.com/');
	
				
			/**
			 * Check authorisation code
			*/
			if (isset($_GET['code']))
			{
				$this->getClient()->authenticate($_GET['code']);
					
				$this->session->setAccessToken
				(
					$this->client->getAccessToken()
				);
					
				header('Location: ' . $this->helper->getUrl("adminhtml/system_config/edit", array('section' => 'ec')));
				exit();
			}
				
			/**
			 * Check session access token
				*/
			$token = $this->session->getAccessToken();
	
			if ($token)
			{
				$this->client->setAccessToken($token);
			}
		}
	
		return $this->client;
	}
	
	/**
	 * Create Google Tag Manager entries
	 *
	 * @param string $entry
	 * @return mixed|NULL
	 */
	public function create($entry)
	{
		if (method_exists($this, $entry))
		{
			/**
			 * Get scope code
			 * 
			 * @var string
			 */
			return call_user_func_array(array($this, $entry), array
			(
				trim($this->scope->getConfig('ec/api/google_gtm_account_id')),
				trim($this->scope->getConfig('ec/api/google_gtm_container'))
			));
		}
	
		return array();
	}

	/**
	 * Create container variables
	 */
	public function ec_api_variables($account, $container)
	{
		$log = [];
		$set = [];
	
		/**
		 * Get existing variables
		*/
		$variables = $this->getService()->accounts_containers_variables->listAccountsContainersVariables($account, $container)->getVariables();
		
		/**
		 * Check which variables already exist
		*/
		foreach ($variables as $variable)
		{
			$set[$variable->name] = true;
		}
		
		/**
		 * Variables schema
		 */
		$schema = array
		(
			'ua' => array
			(
				'name' 		=> 'ua',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'value',
						'value' => $this->scope->getConfig('ec/api/google_gtm_ua')
						
					)
				)
			),
			'google_tag_params' => array
			(
				'name' 		=> 'google_tag_params',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'google_tag_params'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'timing category' => array
			(
				'name' 		=> 'timing category',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'timingCategory'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'timing label' => array
			(
				'name' 		=> 'timing label',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'timingLabel'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'timing var' => array
			(
				'name' 		=> 'timing var',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'timingVar'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'timing value' => array
			(
				'name' 		=> 'timing value',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'timingValue'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'social network' => array
			(
				'name' 		=> 'social network',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'socialNetwork'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'social action' => array
			(
				'name' 		=> 'social action',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'socialAction'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'social target' => array
			(
				'name' 		=> 'social target',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'socialTarget'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'visitor' => array
			(
				'name' 		=> 'visitor',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'visitorId'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'label' => array
			(
				'name' 		=> 'label',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'eventLabel'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'ee transaction id' => array
			(
				'name' 		=> 'ee transaction id',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'ecommerce.purchase.actionField.id'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'ee transaction revenue' => array
			(
				'name' 		=> 'ee transaction revenue',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'ecommerce.purchase.actionField.revenue'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'ee transaction products array' => array
			(
				'name' 		=> 'ee transaction products array',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_DATALAYER_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'name',
						'value' => 'ecommerce.purchase.products'
					),
					array
					(
						'type' 	=> 'integer',
						'key' 	=> 'dataLayerVersion',
						'value' => 2
					)
				)
			),
			'ee conversion id' => array
			(
				'name' 		=> 'ee conversion id',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE,
				'parameter' => array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'value',
						'value' => $this->getAdwordsConversionId()
					)
				)
			),
			'ee conversion label' => 
			[
				'name' 		=> 'ee conversion label',
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_CONSTANT_VARIABLE,
				'parameter' => 
				[
					[
						'type' 	=> 'template',
						'key' 	=> 'value',
						'value' => $this->getAdWordsConversionLabel()
					]
				]
			],
			self::VARIABLE_GA => 
			[
				'name' 		=> self::VARIABLE_GA,
				'type'		=> self::TAG_MANAGER_VARIABLE_TYPE_GA_SETTINGS,
				'parameter' =>
				[
					[
						'type' 	=> 'template',
						'key' 	=> 'trackingId',
						'value' => '{{ua}}'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'cookieDomain',
						'value' => 'auto'
					],
					[
						'type' 	=> 'boolean',
						'key' 	=> 'enableEcommerce',
						'value' => true
					],
					[
						'type' 	=> 'boolean',
						'key' 	=> 'useEcommerceDataLayer',
						'value' => true
					],
					[
						'type' 	=> 'list',
						'key' 	=> 'fieldsToSet',
						'list' 	=> 
						[
							[
								'type' 	=> 'map',
								'map' 	=> 
								[
									[
										'type' 	=> 'template',
										'key' 	=> 'fieldName',
										'value' => 'userId'
									],
									[
										'type' 	=> 'template',
										'key' 	=> 'value',
										'value' => '{{visitor}}'
									]
								]
							]
						]
					]
				]
			]
		);

		foreach ($schema as $variable => $parameters)
		{
			try
			{
				if (!isset($set[$variable]))
				{
					$response = $this->getService()->accounts_containers_variables->create($account, $container, new Google_Service_TagManager_Variable($parameters));
						
					if ($response instanceof Google_Service_TagManager_Variable && $response->variableId)
					{
						$log[] = 'Created variable ' . $response->name;
					}
					else
					{
						$log[] = 'Failed to create variable ' . $response->name;
					}
				}
			}
			catch (\Exception $e)
			{
				$log[] = $e->getMessage();
			}
		}
	
		return $log;
	}
	
	/**
	 * Create container triggers
	 */
	public function ec_api_triggers($account, $container)
	{
		$log = [];
		$set = [];
	
		$triggers = $this->getService()->accounts_containers_triggers->listAccountsContainersTriggers($account, $container)->getTriggers();
	
		foreach ($triggers as $trigger)
		{
			$set[$trigger->name] = true;
		}
		/**
		 * Triggers schema
		 */
		$schema = array
		(
			self::TRIGGER_ADD_TO_CART => array
			(
				'name' 				=> self::TRIGGER_ADD_TO_CART,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'addToCart'
							)
						)
					)
				)
			),
			self::TRIGGER_REMOVE_FROM_CART => array
			(
				'name' 				=> self::TRIGGER_REMOVE_FROM_CART,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'removeFromCart'
							)
						)
					)
				)
			),
			self::TRIGGER_PRODUCT_CLICK => array
			(
				'name' 				=> self::TRIGGER_PRODUCT_CLICK,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'productClick'
							)
						)
					)
				)
			),
			self::TRIGGER_PROMOTION_CLICK => array
			(
				'name' 				=> self::TRIGGER_PROMOTION_CLICK,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'promotionClick'
							)
						)
					)
				)
			),
			self::TRIGGER_PROMOTION_VIEW => array
			(
				'name' 				=> self::TRIGGER_PROMOTION_VIEW,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'promoViewNonInteractive'
							)
						)
					)
				)
			),
			self::TRIGGER_NEW_WIDGET_VIEW => array
			(
				'name' 				=> self::TRIGGER_NEW_WIDGET_VIEW,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'widgetViewNonInteractive'
							)
						)
					)
				)
			),
			self::TRIGGER_CHECKOUT => array
			(
				'name' 				=> self::TRIGGER_CHECKOUT,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'checkout'
							)
						)
					)
				)
			),
			self::TRIGGER_CHECKOUT_OPTION => array
			(
				'name' 				=> self::TRIGGER_CHECKOUT_OPTION,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'checkoutOption'
							)
						)
					)
				)
			),
			self::TRIGGER_SOCIAL_INTERACTION => array
			(
				'name' 				=> self::TRIGGER_SOCIAL_INTERACTION,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'socialInt'
							)
						)
					)
				)
			),
			self::TRIGGER_REMARKETING_TAG => array
			(
				'name' 				=> self::TRIGGER_REMARKETING_TAG,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'fireRemarketingTag'
							)
						)
					)
				)
			),
			self::TRIGGER_USER_TIMING => array
			(
				'name' 				=> self::TRIGGER_USER_TIMING,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'trackTime'
							)
						)
					)
				)
			),
			self::TRIGGER_IMPRESSION => array
			(
				'name' 				=> self::TRIGGER_IMPRESSION,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'impression'
							)
						)
					)
				)
			),
			self::TRIGGER_ADD_WISHLIST => array
			(
				'name' 				=> self::TRIGGER_ADD_WISHLIST,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'addToWishlist'
							)
						)
					)
				)
			),
			self::TRIGGER_ADD_COMPARE => array
			(
				'name' 				=> self::TRIGGER_ADD_COMPARE,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'addToCompare'
							)
						)
					)
				)
			),
			self::TRIGGER_COOKIE_CONSENT_GRANTED => array
			(
				'name' 				=> self::TRIGGER_COOKIE_CONSENT_GRANTED,
				'type'				=> self::TAG_MANAGER_TRIGGER_TYPE_CUSTOM_EVENT,
				'customEventFilter' => array
				(
					array
					(
						'type' => 'equals',
						'parameter' => array
						(
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg0',
								'value' => '{{_event}}'
							),
							array
							(
								'type' 	=> 'template',
								'key' 	=> 'arg1',
								'value' => 'cookieConsentGranted'
							)
						)
					)
				)
			)
		);
	
		foreach ($schema as $trigger => $parameters)
		{
			try
			{
				if (!isset($set[$trigger]))
				{
					$response = $this->getService()->accounts_containers_triggers->create($account, $container, new Google_Service_TagManager_Trigger($parameters));
						
					if ($response instanceof Google_Service_TagManager_Trigger && $response->triggerId)
					{
						$log[] = 'Created trigger ' . $response->name;
					}
					else
					{
						$log[] = 'Failed to create trigger ' . $response->name;
					}
				}
			}
			catch (Exception $e)
			{
				$log[] = $e->getMessage();
			}
		}
	
		return $log;
	}
	
	/**
	 * Create tags
	 *
	 * @param string $account
	 * @param int $container
	 */
	public function ec_api_tags($account, $container)
	{
		$log = [];
		$set = [];
	
		$tags = $this->getService()->accounts_containers_tags->listAccountsContainersTags($account, $container)->getTags();

		foreach ($tags as $tag)
		{
			$set[$tag->name] = true;
		}
	
		/**
		 * Get available triggers
		 */
		$triggers = $this->getTriggersMap($account, $container);
	
		$schema = array
		(
			self::TAG_ADD_TO_CART => array
			(
				'name' 				=> self::TAG_ADD_TO_CART,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_ADD_TO_CART]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Add To Cart'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_REMOVE_FROM_CART => array
			(
				'name' 				=> self::TAG_REMOVE_FROM_CART,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_REMOVE_FROM_CART]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Remove From Cart'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_PRODUCT_CLICK => array
			(
				'name' 				=> self::TAG_PRODUCT_CLICK,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_PRODUCT_CLICK]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Product Click'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					),
					array
					(
						'type' 	=> 'boolean',
						'key' 	=> 'enableEcommerce',
						'value' => true
					),
					array
					(
						'type' 	=> 'boolean',
						'key' 	=> 'useEcommerceDataLayer',
						'value' => true
					)
				)
			),
			self::TAG_PROMOTION_CLICK => array
			(
				'name' 				=> self::TAG_PROMOTION_CLICK,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_PROMOTION_CLICK]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Promotion Click'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_PROMOTION_VIEW => array
			(
				'name' 				=> self::TAG_PROMOTION_VIEW,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_PROMOTION_VIEW]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Promotion'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Promotion View'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					),
					array
					(
						'type' 	=> 'boolean',
						'key' 	=> 'nonInteraction',
						'value' => true
					)
				)
			),
			self::TAG_NEW_WIDGET_VIEW => array
			(
				'name' 				=> self::TAG_NEW_WIDGET_VIEW,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_NEW_WIDGET_VIEW]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'NewProduct Widget'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'NewProduct Widget View'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					),
					array
					(
						'type' 	=> 'boolean',
						'key' 	=> 'nonInteraction',
						'value' => true
					)
				)
			),
			self::TAG_CHECKOUT => array
			(
				'name' 				=> self::TAG_CHECKOUT,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_CHECKOUT]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Checkout'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_CHECKOUT_OPTION => array
			(
				'name' 				=> self::TAG_CHECKOUT_OPTION,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_CHECKOUT_OPTION]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Checkout Option'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_SOCIAL_INTERACTION => array
			(
				'name' 				=> self::TAG_SOCIAL_INTERACTION,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_SOCIAL_INTERACTION]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_SOCIAL'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'socialNetwork',
						'value' => '{{social network}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'socialAction',
						'value' => '{{social action}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'socialActionTarget',
						'value' => '{{social target}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventLabel',
						'value' => '{{label}}'
					)
				)
			),
			self::TAG_USER_TIMING => array
			(
				'name' 				=> self::TAG_USER_TIMING,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_USER_TIMING]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_TIMING'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'timingCategory',
						'value' => '{{timing category}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'timingVar',
						'value' => '{{timing var}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'timingLabel',
						'value' => '{{timing label}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'timingValue',
						'value' => '{{timing value}}'
					)
				)
			),
			self::TAG_IMPRESSION => array
			(
				'name' 				=> self::TAG_IMPRESSION,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_IMPRESSION]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Impression'
					)
				)
			),
			self::TAG_ADD_TO_WISHLIST => array
			(
				'name' 				=> self::TAG_ADD_TO_WISHLIST,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_ADD_WISHLIST]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Wishlist'
					)
				)
			),
			self::TAG_ADD_TO_COMPARE => array
			(
				'name' 				=> self::TAG_ADD_TO_COMPARE,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_ADD_COMPARE]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
				'parameter' 		=> array
				(
					[
						'type' 	=> 'boolean',
						'key' 	=> 'overrideGaSettings',
						'value' => 'false'
					],
					[
						'type' 	=> 'template',
						'key' 	=> 'gaSettings',
						'value' => $this->getGoogleAnalyticsSettings()
					],
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'trackType',
						'value' => 'TRACK_EVENT'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventCategory',
						'value' => 'Ecommerce'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'eventAction',
						'value' => 'Compare'
					)
				)
			),
			self::TAG_ADWORDS_DYNAMIC_REMARKETING => array
			(
				'name' 				=> self::TAG_ADWORDS_DYNAMIC_REMARKETING,
				'firingTriggerId' 	=> array
				(
					$triggers[self::TRIGGER_REMARKETING_TAG]
				),
				'type' 				=> self::TAG_MANAGER_TAG_TYPE_ADWORDS_DYNAMIC_REMARKETING,
				'parameter' 		=> array
				(
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'googleScreenName',
						'value' => self::TAG_ADWORDS_DYNAMIC_REMARKETING
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'conversionId',
						'value' => '{{ee conversion id}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'conversionLabel',
						'value' => '{{ee conversion label}}'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'customParamsFormat',
						'value' => 'DATA_LAYER'
					),
					array
					(
						'type' 	=> 'template',
						'key' 	=> 'dataLayerVariable',
						'value' => '{{google_tag_params}}'
					)	
				)
			)
		);
		
		/**
		 * Create Universal Analytics tag (optional)
		 */
		if ($this->scope->getRequest()->getParam('ec_api_ua'))
		{
			$exists = false;
			
			foreach ($tags as $tag)
			{
				if (in_array(self::TRIGGER_ALL_PAGES, $tag->getFiringTriggerId()))
				{
					$exists = true;
				}
			}

			if (!$exists)
			{
				$schema[self::TAG_UA] = 
				[
					
					'name' 				=> self::TAG_UA,
					'firingTriggerId' 	=> 
					[
						self::TRIGGER_ALL_PAGES
					],
					'type' 				=> self::TAG_MANAGER_TAG_TYPE_UNIVERSAL_ANALYTICS,
					'parameter' 		=> 
					[
						[
							'type' 	=> 'boolean',
							'key' 	=> 'overrideGaSettings',
							'value' => 'false'
						],
						[
							'type' 	=> 'template',
							'key' 	=> 'gaSettings',
							'value' => $this->getGoogleAnalyticsSettings()
						],
						[
							'type' 	=> 'template',
							'key' 	=> 'trackType',
							'value' => 'TRACK_PAGEVIEW'
						]
					]
				];
			}
			else 
			{
				$this->messageManager->addComplexWarningMessage('addLogSuccessMessage',
				[
					'text' => __('We have detected existing Universal Analytics tag. Skipping tag creation.')->__toString()
				]);
			}
		}
	
		foreach ($schema as $tag => $parameters)
		{
			try
			{
				if (!isset($set[$tag]))
				{
					$response = $this->getService()->accounts_containers_tags->create($account, $container, new Google_Service_TagManager_Tag($parameters));
	
					if ($response instanceof Google_Service_TagManager_Tag && $response->tagId)
					{
						$log[] = 'Created tag ' . $response->name;
					}
					else
					{
						$log[] = 'Failed to create tag ' . $response->name;
					}
				}
			}
			catch (\Exception $e)
			{
				$log[] = $e->getMessage();
			}
		}
	
		return $log;
	}
	
	public function ec_api_version($account, $container)
	{
		$log = [];
		$set = [];
	
		$versions = $this->getService()->accounts_containers_versions->listAccountsContainersVersions($account, $container);
	
		return true;
	}
	
	public function getService()
	{
		if (!$this->service)
		{
			$this->service = new Google_Service_TagManager
			(
				$this->getClient()
			);
		}
	
		return $this->service;
	}
	
	public function getOauth()
	{
		if (!$this->oauth)
		{
			$this->oauth = new Google_Service_Oauth2
			(
				$this->getClient()
			);
		}
	
		return $this->oauth;
	}
	
	/**
	 * Get account containers 
	 * 
	 * @param int $account
	 * @return array
	 */
	public function getContainers($account)
	{
		if ($this->getClient()->isAccessTokenExpired())
		{
			return array();
		}
	
		if (!$this->containers)
		{
			$this->containers = $this->getService()->accounts_containers->listAccountsContainers($account)->getContainers();
		}

		return $this->containers;
	}
	
	/**
	 * Get containers map 
	 * 
	 * @param int $account
	 * @return NULL[]
	 */
	public function getContainersMap($account)
	{
		$map = [];
		
		foreach ($this->getContainers($account) as $container) 
		{
			$map[$container->containerId] = $container->publicId;
		}
		
		return $map;
	}
	
	public function getTriggers($account, $container)
	{
		return $this->getService()->accounts_containers_triggers->listAccountsContainersTriggers($account, $container)->getTriggers();
	}
	
	public function getTriggersMap($account, $container)
	{
		$map = [];
	
		foreach ($this->getTriggers($account, $container) as $trigger)
		{
			$map[$trigger->name] = $trigger->triggerId;
		}
	
		return $map;
	}
	
	/**
	 * Get safe conversion id
	 */
	protected function getAdwordsConversionId()
	{
		$value = $this->scope->getConfig('ec/api/google_adwords_conversion_id');
			
		if ('' == $value)
		{
			return 0;
		}
	
		return $value;
	}
	
	/**
	 * Get safe conversion label
	 */
	protected function getAdwordsConversionLabel()
	{
		$value = $this->scope->getConfig('ec/api/google_adwords_conversion_label');
		
		if ('' == $value)
		{
			return 0;
		}
	
		return $value;
	}
	
	protected function getGoogleAnalyticsSettings()
	{
		return '{{' . self::VARIABLE_GA . '}}';
	}
}