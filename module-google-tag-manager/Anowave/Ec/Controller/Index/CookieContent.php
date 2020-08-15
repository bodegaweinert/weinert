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
 
namespace Anowave\Ec\Controller\Index;

class CookieContent extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\Directive
	 */
	protected $directive;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\DirectiveMarketing
	 */
	protected $directiveMarketing;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\DirectivePreferences
	 */
	protected $directivePreferences;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\DirectiveAnalytics
	 */
	protected $directiveAnalytics;
	
	/**
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $blockFactory;
	
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Model\Cookie\Directive $directive
	 * @param \Anowave\Ec\Model\Cookie\DirectiveMarketing $directiveMarketing
	 * @param \Anowave\Ec\Model\Cookie\DirectivePreferences $directivePreferences
	 * @param \Anowave\Ec\Model\Cookie\DirectiveAnalytics $directiveAnalytics
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Model\Cookie\Directive $directive,
		\Anowave\Ec\Model\Cookie\DirectiveMarketing $directiveMarketing,
		\Anowave\Ec\Model\Cookie\DirectivePreferences $directivePreferences,
		\Anowave\Ec\Model\Cookie\DirectiveAnalytics $directiveAnalytics,
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	)
	{
		parent::__construct($context);
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data
		 */
		$this->helper = $helper;
		
		/**
		 * Set cookie directive
		 *
		 * @var \Anowave\Ec\Model\Cookie\Directive $directive
		 */
		$this->directive = $directive;
		
		/**
		 * Set cookie directive 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\DirectiveMarketing $directiveMarketing
		 */
		$this->directiveMarketing = $directiveMarketing;
		
		/**
		 * Set cookie directive 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\DirectivePreferences $directivePreferences
		 */
		$this->directivePreferences = $directivePreferences;
		
		/**
		 * Set cookie directive 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\DirectiveAnalytics $directiveAnalytics
		 */
		$this->directiveAnalytics = $directiveAnalytics;
		
		/**
		 * Set block factory 
		 * 
		 * @var \Magento\Framework\View\Element\BlockFactory $blockFactory
		 */
		$this->blockFactory = $blockFactory;
		
		/**
		 * Set result factory 
		 * 
		 * @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
		 */
		$this->resultJsonFactory = $resultJsonFactory;
	}

	/**
	 * Execute controller
	 *
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{
		$cookie = (int) $this->directive->get();
		
		/**
		 * Cookie consent scopes
		 * 
		 * @var array $scope
		 */
		$scope =
		[
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT 				=> $this->directive->get(),
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT 	=> $this->directiveMarketing->get(),
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT 	=> $this->directivePreferences->get(),
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT 	=> $this->directiveAnalytics->get()
		];
		
		/**
		 * Get current granted consent scope
		 * 
		 * @var array $grant
		 */
		$grant = array_filter($scope);

		$segments = [];
		
		foreach ($scope as $key => $value)
		{
			switch ($key)
			{
				case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT:
					
					$segments[\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT] =
					[
						'label' => __('Allow marketing cookies'),
						'value' => __('Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.'), 
						'check' => true
					];
					
					break;
				case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT:
					
					$segments[\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT] =
					[
						'label' => __('Allow preferences cookies'),
						'value' => __('Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.'), 
						'check' => true
					];
					
					break;
				case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT:
					
					$segments[\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT] =
					[
						'label' => __('Allow analytics cookies'),
						'value' => __('Statistic cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.'), 
						'check' => true
					];
					
					break;
			}
		}

		return $this->resultJsonFactory->create()->setData
		(
			[
				'cookieContent' => $this->blockFactory->createBlock('Anowave\Ec\Block\Cookie')->setTemplate('cookiecontent.phtml')->setData(['segments' => $segments])->toHtml()
			]
		);
	}
}