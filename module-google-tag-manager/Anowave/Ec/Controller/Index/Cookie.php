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

class Cookie extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;
	
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
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Model\Cookie\Directive $directive
	 * @param \Anowave\Ec\Model\Cookie\DirectiveMarketing $directiveMarketing
	 * @param \Anowave\Ec\Model\Cookie\DirectivePreferences $directivePreferences
	 * @param \Anowave\Ec\Model\Cookie\DirectiveAnalytics $directiveAnalytics
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Model\Cookie\Directive $directive,
		\Anowave\Ec\Model\Cookie\DirectiveMarketing $directiveMarketing, 
		\Anowave\Ec\Model\Cookie\DirectivePreferences $directivePreferences,
		\Anowave\Ec\Model\Cookie\DirectiveAnalytics $directiveAnalytics
	)
	{
		parent::__construct($context);
		
		/**
		 * Set response type factory 
		 * 
		 * @var \Magento\Framework\Controller\Result\JsonFactory
		 */
		$this->resultJsonFactory = $resultJsonFactory;
		
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
	}

	/**
	 * Execute controller
	 *
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{ 
		$lifetime = (3600 * 24 * 30);
		
		/**
		* Set cookie consent
		*/
		$this->directive->set(1, $lifetime);
		
		$grant = 
		[
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT => true
		];
		
		if ($this->helper->getCookieDirectiveIsSegmentMode())
		{
			/**
			 * Get segments
			 * 
			 * @var [] $segments
			 */
			if ($this->getRequest()->getParam('cookie'))
			{
				$segments = (array) $this->getRequest()->getParam('cookie');
			}
			else 
			{
				$segments = [];
			}
			
			/**
			 * Delete previously granted segments
			 */
			foreach ($this->helper->getCookieDirectiveConsentSegments() as $segment)
			{
				if (!in_array($segment, $segments))
				{
					switch($segment)
					{
						case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT: 		$this->directiveMarketing->delete(); 	break;
						case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT:	$this->directivePreferences->delete(); 	break;
						case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT:		$this->directiveAnalytics->delete(); 	break;
					}
				}
			}
			
			foreach ($segments as $segment)	
			{
				switch($segment)
				{
					case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT: 		$this->directiveMarketing->set(1, $lifetime); 	break;
					case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT:	$this->directivePreferences->set(1, $lifetime); break;
					case \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT:		$this->directiveAnalytics->set(1, $lifetime); 	break;
				}
				
				$grant[$segment] = true;
			}
		}
		
		return $this->resultJsonFactory->create()->setData($grant);
	}
}