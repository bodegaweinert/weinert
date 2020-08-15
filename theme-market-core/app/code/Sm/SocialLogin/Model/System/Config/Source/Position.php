<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Model\System\Config\Source;

/**
 * Class Position
 * @package Sm\SocialLogin\Model\System\Config\Source
 */
class Position implements \Magento\Framework\Option\ArrayInterface
{
	const PAGE_LOGIN = 1;
	const PAGE_CREATE = 2;
	const PAGE_POPUP = 3;
	const PAGE_AUTHEN = 4;

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => '', 'label' => __('-- Please Select --')],
			['value' => self::PAGE_LOGIN, 'label' => __('Customer Login Page')],
			['value' => self::PAGE_CREATE, 'label' => __('Customer Create Page')],
			['value' => self::PAGE_POPUP, 'label' => __('Social Popup Login')],
			['value' => self::PAGE_AUTHEN, 'label' => __('Customer Authentication Popup')]
		];
	}
}
