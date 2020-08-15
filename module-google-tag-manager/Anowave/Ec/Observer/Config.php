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

namespace Anowave\Ec\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Config implements ObserverInterface
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper = null;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager = null;
	
	/**
	 * API 
	 * 
	 * @var \Anowave\Ec\Model\Api
	 */
	protected $api = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Scope
	 */
	protected $scope;
	
	/**
	 * @var \Magento\Framework\Module\Manager
	 */
	protected $moduleManager;

	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Anowave\Ec\Model\Api $api
	 * @param \Anowave\Ec\Helper\Scope $scope
	 * @param \Magento\Framework\Module\Manager $moduleManager
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Anowave\Ec\Model\Api $api,
		\Anowave\Ec\Helper\Scope $scope,
		\Magento\Framework\Module\Manager $moduleManager
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $_helper
		 */
		$this->_helper = $helper;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Magento\Framework\Message\ManagerInterface $_messageManager
		 */
		$this->_messageManager = $messageManager;
		
		/**
		 * Set API 
		 * 
		 * @var \Anowave\Ec\Model\Api $api
		 */
		$this->api = $api;
		
		/**
		 * Set module manager 
		 * 
		 * @var \Magento\Framework\Module\Manager $moduleManager
		 */
		$this->moduleManager = $moduleManager;
		
		$this->scope = $scope;
	}
	
	/**
	 * Add order information into GA block to render on checkout success pages
	 *
	 * @param EventObserver $observer
	 * @return void
	 */
	public function execute(EventObserver $observer)
	{
		$this->_helper->notify($this->_messageManager);
		
		/**
		 * Get API args 
		 * 
		 * @var Ambigous <mixed, \Zend\Stdlib\ParametersInterface> $args
		 */
		$args = $this->scope->getRequest()->getParam('args');
		
		if ($args && $this->scope->getRequest()->getParam('groups')['api']['fields'])
		{
			$errors = [];
			
			/**
			 * Conatiner & Account validation
			 */
			
			if ('' === (string) $this->scope->getConfig('ec/api/google_gtm_account_id'))
			{
				$errors[] = __('Google Tag Manager Account ID is not set.');
			}
			
			if ('' === (string) $this->scope->getConfig('ec/api/google_gtm_ua'))
			{
				$errors[] = __('Google Tag Manager Universal Analytics Tracking ID is not set.');
			}
			
			if ('' === (string) $this->scope->getConfig('ec/api/google_gtm_container'))
			{
				$errors[] = __('Google Tag Manager Container ID is not set.');
			}
			else 
			{
				if (!is_numeric($this->scope->getConfig('ec/api/google_gtm_container')))
				{
					$errors[] = __('Google Tag Manager Container ID should be numeric value.');
				}
			}
			
			if ($errors)
			{
				foreach ($errors as $error)
				{
					$this->_messageManager->addErrorMessage($error);
				}
				
				return true;
			}
			
			/**
			 * Operation log
			*/
			$log = [];
			
			foreach ($args as $entry)
			{
				$log = array_merge($log, $this->getApi()->create($entry));
			}
			
			if (!$log || !$errors)
			{
				$map = $this->getApi()->getContainersMap($this->scope->getConfig('ec/api/google_gtm_account_id'));
				
				$container = $this->scope->getConfig('ec/api/google_gtm_container');
				
				if ($log)
				{
					$log[] = PHP_EOL;
				}
				
				$log[] = "Container ({$map[$container]}) configured succesfully. Please go to <a href='https://tagmanager.google.com/#/container/accounts/{$this->_helper->getConfig('ec/api/google_gtm_account_id')}/containers/{$this->_helper->getConfig('ec/api/google_gtm_container')}' target='_blank'>Google Tag Manager</a> to preview newly created tags, variables and triggers.";
			}
			
			if ($log)
			{
				$this->_messageManager->addComplexNoticeMessage('addLogSuccessMessage',
				[
					'text' => nl2br(join(PHP_EOL, $log))
				]);
			}
		}
		
		if ('' !== (string) $this->scope->getConfig('ec/general/code'))
		{
			$this->_messageManager->addErrorMessage('It seems you are using older version of GTM snippet. Please update using the splitted version provided by Google Tag Manager otherwise tracking may not work.');
		}
		
		$addons = 
		[
			'Amasty_Checkout' => 
			[
				'Anowave_Ecoam' => __('It seems you are using checkout solution from Amasty')
			],
			'Mageplaza_Osc' =>
			[
				'Anowave_Ecopz' => __('It seems you are using checkout solution from Mageplaza')
			],
			'IWD_Opc' =>
			[
				'Anowave_Ecosc' => __('It seems you are using checkout solution from IWD')
			],
		];

		foreach ($addons as $module => $dependency)
		{
			list($addon, $message) = array_merge(array_keys($dependency), array_values($dependency));
			
			if ($this->moduleManager->isEnabled($module) && !$this->moduleManager->isEnabled($addon))
			{
				$this->_messageManager->addWarningMessage("$message. Checkout tracking may not work correctly. Please contact our Help Desk for further support.");
			}
		}

		return true;
	}
	
	/**
	 * Get API 
	 * 
	 * @return \Anowave\Ec\Model\Api
	 */
	protected function getApi()
	{	
		return $this->api;
	}
	
	/**
	 * Validate 
	 * 
	 * @param array $data
	 * @return boolean
	 */
	protected function validate(array $data = [])
	{
		$errors = [];

		if ('' === $data['google_gtm_ua']['value'])
		{
			$errors[] = __('Please provide valid Universal Analytics Tracking ID');
		}
		
		if ('' == $data['google_gtm_account_id']['value'])
		{
			$errors[] = __('Please provide valid GTM Account ID');
		}
		
		if ('' == $data['google_gtm_container']['value'])
		{
			$errors[] = __('Please provide valid GTM Container ID');
		}
		else 
		{
			if (!is_numeric($data['google_gtm_container']['value']))
			{
				$errors[] = __('GTM Container ID is invalid. Expected numeric value.');
			}
		}
		
		if (!$errors)
		{
			return true;
		}
		
		foreach ($errors as $error) 
		{
			$this->_messageManager->addErrorMessage($error);
		}
		
		return false;
	}
}