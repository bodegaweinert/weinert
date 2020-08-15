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

namespace Anowave\Ec\Helper;

use Anowave\Package\Helper\Package;

class Constants extends \Anowave\Package\Helper\Package
{
	/**
	 * Checkout shipping step 
	 * 
	 * @var integer
	 */
	const CHECKOUT_STEP_SHIPPING = 1;
	
	/**
	 * Checkout payment step 
	 * 
	 * @var integer
	 */
	const CHECKOUT_STEP_PAYMENT  = 2;
	
	/**
	 * Checkout place order step 
	 * 
	 * @var integer
	 */
	const CHECKOUT_STEP_ORDER = 3;
	
	/*
	 * Related products list name/category
	 */
	const LIST_RELATED 	= 'Related products';
	
	/*
	 * Cross sells list name/category
	 */
	const LIST_CROSS_SELL = 'Cross Sells';
	
	/*
	 * Upsells products list name/category
	 */
	const LIST_UP_SELL 	= 'Up Sells';
	
	/**
	 * Impression item selector
	 * 
	 * @var string
	 */
	const XPATH_LIST_SELECTOR = '//ol[contains(@class, "products")]/li';
	
	/**
	 * Impression item click selector 
	 * 
	 * @var string
	 */
	const XPATH_LIST_CLICK_SELECTOR = 'div/a';
	
	/**
	 * Add to cart button selector
	 * 
	 * @var string
	 */
	const XPATH_CART_SELECTOR = '//button[@id="product-addtocart-button"]';
	
	/**
	 * Add to cart button from categories selector
	 * 
	 * @var string
	 */
	const XPATH_CART_CATEGORY_SELECTOR 	= 'div/div/div/div/div/form/button[contains(@class,"tocart")]';
	
	/**
	 * Remove from cart selector
	 * 
	 * @var string
	 */
	const XPATH_CART_DELETE_SELECTOR = '//a[contains(@class,"action-delete")]|//a[contains(@class,"remove")]';
	
	/**
	 * Widget selector
	 *
	 * @var string
	 */
	const XPATH_LIST_WIDGET_SELECTOR = '//ol[contains(@class,"product-items")]/li';
	
	/**
	 * Widget click selector
	 *
	 * @var string
	 */
	const XPATH_LIST_WIDGET_CLICK_SELECTOR = 'div/a[contains(@class,"product-item-photo")]';
	
	/**
	 * Widget cart add selector
	 *
	 * @var string
	 */
	const XPATH_LIST_WIDGET_CART_SELECTOR = 'div/div/div/div/button[contains(@class,"tocart")]';
	
	/**
	 * Add to wishlist selector
	 * 
	 * @var string
	 */
	const XPATH_ADD_WISHLIST_SELECTOR = '//a[contains(@class,"towishlist")]';
	
	
	/**
	 * Add to wishlist selector
	 * 
	 * @var string
	 */
	const XPATH_ADD_COMPARE_SELECTOR = '//a[contains(@class,"tocompare")]';
	
	/**
	 * Internal search dimension index
	 * 
	 * @var integer
	 */
	const INTERNAL_SEARCH_DEFAULT_DIMENSION = 18;
	
	/**
	 * Cookie consent granted event
	 * @var string
	 */
	const COOKIE_CONSENT_GRANTED_EVENT = 'cookieConsentGranted';
	
	/**
	 * Cookie consent granted event
	 * @var string
	 */
	const COOKIE_CONSENT_MARKETING_GRANTED_EVENT = 'cookieConsentMarketingGranted';
	
	/**
	 * Cookie consent granted event
	 * @var string
	 */
	const COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT = 'cookieConsentPreferencesGranted';
	
	/**
	 * Cookie consent granted event
	 * @var string
	 */
	const COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT = 'cookieConsentAnalyticsGranted';	
}