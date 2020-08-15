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

namespace Anowave\Ec\Model\System\Config\Source;

class Id implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Id 
	 * 
	 * @var string
	 */
	const ID_ID = 'id';
	
	/**
	 * SKU 
	 * 
	 * @var string
	 */
	const ID_SKU = 'sku';
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => self::ID_ID, 
				'label' => __('ID')
			],
			[
				'value' => self::ID_SKU, 
				'label' => __('SKU')
			]
		];
	}
}