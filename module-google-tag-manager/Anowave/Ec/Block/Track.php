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

namespace Anowave\Ec\Block;

class Track extends \Magento\Framework\View\Element\Template
{
	/**
	 * Google Tag Manager Data
	 *
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper;
	
	/**
	 * @var \Magento\Framework\DataObject
	 */
	protected $adwords;
	
	/**
	 * @var \Anowave\Ec\Helper\Datalayer
	 */
	protected $dataLayer;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
		array $data = []
	) 
	{
		/**
		 * Set Helper
		 * @var \Anowave\Ec\Helper\Data 
		 */
		$this->_helper = $helper;
		
		/**
		 * Set dataLayer 
		 * 
		 * @var \Anowave\Ec\Helper\Datalayer
		 */
		$this->dataLayer = $dataLayer;
		
		/**
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Parent constructor
		 */
		parent::__construct($context, $data);
		
		/**
		 * Make block non-cachable
		 * 
		 * @var boolean
		 */
		$this->_isScopePrivate = false;
	}
	
	/**
	 * Make block non-cachable
	 *
	 * @see \Magento\Framework\View\Element\AbstractBlock::isScopePrivate()
	 */
	public function isScopePrivate()
	{
		return false;
	}
	
	/**
	 * Get helper
	 * 
	 * @return \Anowave\Ec\Helper\Data
	 */
	public function getHelper()
	{
		if ($this->_helper)
		{
			return $this->_helper;
		}
		else 
		{
			throw new \Exception('\Anowave\Ec\Helper\Data is not instantiated.');
		}
	}
	
	/**
	 * Get sales order collection
	 * 
	 * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	public function getSalesOrderCollection()
	{
		return $this->_helper->getSalesOrderCollection();
	}

	/**
	 * Determine page type
	 * 
	 * @return string
	 */
	public function getPageType()
	{
		switch($this->getRequest()->getControllerName())
		{
			case 'index':		return 'home';
			case 'cart':		return 'cart';
			case 'category':	return 'category';
			case 'product': 	return 'product';
			case 'result':		return 'searchresults';
				default:		return 'other';
		}
	}
	
	/**
	 * Get purchase push
	 *
	 * @return JSON|false
	 */
	public function getPurchasePush()
	{
		return $this->_helper->getPurchasePush($this);
	}

	/**
	 * Get purchase google tag params
	 *
	 * @return JSON|false
	 */
	public function getPurchaseGoogleTagParams()
	{
		return $this->_helper->getPurchaseGoogleTagParams($this);
	}
	
	/**
	 * Get visitor push
	 *
	 * @return JSON|false
	 */
	public function getVisitorPush()
	{
		return $this->_helper->getVisitorPush($this);
	}
	
	/**
	 * Get details push
	 *
	 * @return JSON|false
	 */
	public function getDetailPushForward()
	{
		return $this->_helper->getDetailPushForward($this);
	}

	/**
	 * Get impressions push
	 *
	 * @return JSON|false
	 */
	public function getImpressionPushForward()
	{
		return $this->_helper->getImpressionPushForward($this);
	}

	/**
	 * Get search push
	 *
	 * @return JSON|false
	 */
	public function getSearchPush()
	{
		return $this->_helper->getSearchPush($this);
	}
	
	public function getConversion()
	{
		return $this->_helper->getConversion($this);
	}
	
	/**
	 * Get store configuration variable 
	 * 
	 * @param string $config
	 */
	public function getConfig($config)
	{
		return $this->_helper->getConfig($config);
	}
	
	public function getBodySnippet()
	{
		return $this->getHelper()->getBodySnippet();
	}
	
	public function getHeadSnippet()
	{
		return $this->getHelper()->getHeadSnippet();
	}
	
	/**
	 * Get after <body> content
	 * 
	 * @return string
	 */
	public function afterBody()
	{
		return $this->getHelper()->afterBody();
	}

	/**
	 * Get Adwords Data Object
	 * 
	 * @return \Magento\Framework\DataObject
	 */
	public function getAdWords()
	{
		if (!$this->adwords)
		{
			$this->adwords = new \Magento\Framework\DataObject(
			[
				'google_conversion_id' 			=> $this->getConfig('ec/adwords/conversion_id'),
				'google_conversion_label' 		=> $this->getConfig('ec/adwords/conversion_label'),
				'google_conversion_language'	=> $this->getConfig('ec/adwords/conversion_language'),
				'google_conversion_format'		=> $this->getConfig('ec/adwords/conversion_format'),
				'google_conversion_color' 		=> $this->getConfig('ec/adwords/conversion_color'),
				'google_conversion_currency' 	=> $this->getConfig('ec/adwords/conversion_currency')
			]);
		}
		
		return $this->adwords;
	}
	
	/**
	 * Get orders collection
	 * 
	 * @return array
	 */
	public function getOrders()
	{
		return $this->getHelper()->getOrders($this);
	}
	
	/**
	 * Get transaction revenue
	 * 
	 * @return float
	 */
	public function getRevenue()
	{
		$revenue = 0;
		
		foreach ($this->getHelper()->getOrders($this) as $order)
		{
			$revenue = $this->getHelper()->getRevenue($order);
		}
		
		return $revenue;
	}
	
	/**
	 * Get datalayer helper 
	 * 
	 * @return \Anowave\Ec\Helper\Datalayer
	 */
	public function getDatalayer()
	{
		return $this->dataLayer;
	}
	
	/**
	 * Escape strig 
	 * 
	 * @param string $string
	 */
	public function escape($string)
	{
		return $this->_helper->escape($string);
	}
	
	/**
	 * No cache lifetime
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::getCacheLifetime()
	 */
	protected function getCacheLifetime()
	{
		return null;
	}
	
	/**
	 * Do not load from cache
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::_loadCache()
	 */
	protected function _loadCache()
	{
		return false;
	}
	
	/**
	 * Never save cache for this block
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::_saveCache()
	 */
	protected function _saveCache($data)
	{
		return false;
	}
	
	/**
	 * Render GTM
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->_helper->isActive())
		{
			return '';	
		}
		
		return $this->_helper->filter
		(
			parent::_toHtml()
		);
	}
}
