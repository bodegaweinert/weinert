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

class Cookie extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper;

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		array $data = []
	) 
	{
		/**
		 * Set Helper
		 * @var \Anowave\Ec\Helper\Data 
		 */
		$this->_helper = $helper;
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