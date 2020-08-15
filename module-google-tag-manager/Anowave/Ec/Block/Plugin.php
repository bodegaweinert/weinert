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

use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean;

class Plugin
{
	/**
	 * Helper
	 *
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper = null;
	
	/**
	 * Config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_coreConfig = null;
	
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
	
	/**
	 * Cart
	 *
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $_cart = null;
	
	/**
	 * ProductRepository
	 * 
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository = null;
	
	/**
	 * CategoryRepository
	 * 
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * @var \Anowave\Ec\Helper\Attributes
	 */
	protected $attributes;
	
	/**
	 * @var \Anowave\Ec\Helper\Bridge
	 */
	protected $bridge;
	
	/**
	 * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
	 */
	protected $stockItemRepository;
	
	/**
	 * 
	 * @var \Anowave\Ec\Model\Apply
	 */
	private $canApply = false;
	
	/**
	 * @var \Anowave\Ec\Model\Cache
	 */
	private $cache = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Datalayer
	 */
	private $dataLayer = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
	 * @param \Magento\Framework\Registry $registry
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Anowave\Ec\Model\Apply $apply
	 * @param \Anowave\Ec\Model\Cache $cache
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param \Anowave\Ec\Helper\Attributes $attributes
	 * @param \Anowave\Ec\Helper\Bridge $bridge
	 * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
	 */
	public function __construct
	(
		\Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
		\Magento\Framework\Registry $registry, 
		\Anowave\Ec\Helper\Data $helper, 
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Anowave\Ec\Model\Apply $apply,
		\Anowave\Ec\Model\Cache $cache,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		\Anowave\Ec\Helper\Attributes $attributes,
		\Anowave\Ec\Helper\Bridge $bridge,
		\Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
	) 
	{	
		/**
		 * Set cart 
		 * 
		 * @var \Magento\Checkout\Model\Car $_cart
		 */
		$this->_cart = $cart;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $_helper
		 */
		$this->_helper = $helper;
		
		/**
		 * Set Core config
		 * 
		 * @var \Magento\Framework\App\Config\ScopeConfigInterface $_coreConfig
		 */
		$this->_coreConfig = $coreConfig;
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry $_coreRegistry
		 */
		$this->_coreRegistry = $registry;
		
		/**
		 * Set product repository
		 * 
		 * @var unknown
		 */
		$this->productRepository = $productRepository;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set stock item repository 
		 * 
		 * @var \Anowave\Ec\Block\Plugin $stockItemRepository
		 */
		$this->stockItemRepository = $stockItemRepository;
		
		/**
		 * Check if tracking should be applied
		 * 
		 * @var Boolean
		 */
		$this->canApply = $apply->canApply
		(
			$this->_helper->filter('Anowave\Ec\Block\Track')
		);
		
		/**
		 * @var \Anowave\Ec\Model\Cache
		 */
		$this->cache = $cache;
		
		/**
		 * Set dataLayer 
		 * 
		 * @var \Anowave\Ec\Helper\Datalayer
		 */
		$this->dataLayer = $dataLayer;
		
		/**
		 * Set attributes
		 * 
		 * @var \Anowave\Ec\Helper\Attributes $attributes
		 */
		$this->attributes = $attributes;
		
		/**
		 * Set bridge 
		 * 
		 * @var \Anowave\Ec\Helper\Bridge $bridge
		 */
		$this->bridge = $bridge;
	}
	
	/**
	 * Block output modifier
	 *
	 * @param \Magento\Framework\View\Element\Template $block
	 * @param string $html
	 *
	 * @return string
	 */
	public function afterFetchView($block, $content)
	{
		return $content;
	}
	
	/**
	 * Block output modifier 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @param string $html
	 * 
	 * @return string
	 */
	public function afterToHtml($block, $content) 
	{
		if ($this->_helper->isActive() && $this->canApply)
		{
			switch($block->getNameInLayout())
			{
				case 'product.info.addtocart':
				case 'product.info.addtocart.additional':
				case 'product.info.addtocart.bundle':
																					return $this->augmentAddCartBlock($block, $content);
				case 'category.products.list': 										return $this->augmentListBlock($block, $content);
				case 'catalog.product.related':										return $this->augmentListRelatedBlock($block, $content);
				case 'product.info.upsell':											return $this->augmentListUpsellBlock($block, $content);
				case 'view.addto.wishlist':											return $this->augmentWishlistBlock($block, $content);
				case 'wishlist_sidebar':											return $this->augmentWishlistSidebarBlock($block, $content);
				case 'view.addto.compare':											return $this->augmentCompareBlock($block, $content);
				case 'checkout.cart':												return $this->augmentCartBlock($block, $content);
				case 'checkout.root': 												return $this->augmentCheckoutBlock($block, $content);
				case 'checkout.cart.item.renderers.simple.actions.remove':
				case 'checkout.cart.item.renderers.bundle.actions.remove':
				case 'checkout.cart.item.renderers.virtual.actions.remove':
				case 'checkout.cart.item.renderers.default.actions.remove':
				case 'checkout.cart.item.renderers.grouped.actions.remove':
				case 'checkout.cart.item.renderers.downloadable.actions.remove':
				case 'checkout.cart.item.renderers.configurable.actions.remove':    return $this->augmentRemoveCartBlock($block, $content);
				case 'ec_noscript':													return $this->augmentAmp($block, $content);
					default:
						switch(true) 
						{
							case $block instanceof \Magento\Catalog\Block\Product\Widget\NewWidget: 	return $this->augmentWidgetBlock($block, $content);
							case $block instanceof \Magento\CatalogWidget\Block\Product\ProductsList:	return $this->augmentWidgetListBlock($block, $content);
						}
						break;
			}
		}
		
		return $content;
	}

	/**
	 * Modify checkout output 
	 * 
	 * @param AbstractBlock $block
	 * @param string $content
	 * 
	 * @return string
	 */
	protected function augmentCheckoutBlock($block, $content)
	{
		return $content .= $block->getLayout()->createBlock('Anowave\Ec\Block\Track')->setTemplate('checkout.phtml')->setData
		(
			[
				'checkout_push' => $this->_helper->getCheckoutPush($block, $this->_cart, $this->_coreRegistry)
			]
		)
		->toHtml();
	}
	
	/**
	 * Modify cart output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 * 
	 * @return string
	 */
	protected function augmentCartBlock($block, $content)
	{
		return $content .= $block->getLayout()->createBlock('Anowave\Ec\Block\Track')->setTemplate('cart.phtml')->setData
		(
			[
				'cart_push' => $this->_helper->getCartPush($block, $this->_cart, $this->_coreRegistry)
			]
		)
		->toHtml();
	}

	/**
	 * Modify NewProduct widget 
	 * 
	 * @param \Magento\Catalog\Block\Product\Widget\NewWidget $block
	 * @param string $content
	 * @return string
	 */
	public function augmentWidgetBlock(\Magento\Catalog\Block\Product\Widget\NewWidget $block, $content)
	{
		/**
		 * Load cache
		 *
		 * @var string
		 */
		$cache = $this->cache->load(\Anowave\Ec\Model\Cache::CACHE_LISTING_WIDGET);
		
		if ($cache)
		{
			return $cache;
		}
		
		/**
		 * Retrieve list of impression product(s)
		 *
		 * @var array
		 */
		$products = [];
		
		$collection = $block->getProductCollection();
		
		if (!$collection)
		{
			return $content;
		}
		
		foreach ($collection as $product)
		{
			$products[] = $product;
		}
		
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		/**
		 * Default starting position
		 * 
		 * @var integer $position
		 */
		$position = 1;
		
		/**
		 * Default category 
		 * 
		 * @var \Magento\Framework\Phrase $category
		 */
		$category = __('New products')->__toString();
		
		/**
		 * Impression push
		 * 
		 * @var array $impressions
		 */
		$impressions = 
		[
			'event' 	=> 'widgetViewNonInteractive',
			'ecommerce' => 
			[
				'currencyCode' 	=> $this->_helper->getCurrency(),
				'actionField'	=> 
				[
					'list' => $category
				],
				'impressions' 	=> []
			]
		];
		
		foreach ($query->query($this->_helper->getListWidgetSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{
				foreach ($query->query($this->_helper->getListWidgetClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
					
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $category);
					$a->setAttribute('data-list',		$category);
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('data-click',$click);
					}
					
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-position',	$position);
					$a->setAttribute('data-event',		'productClick');
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('onclick','return AEC.click(this,dataLayer)');
					}
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_widget_click_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();

					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
					
					$impressions['ecommerce']['impressions'][] = 
					[
						'id' 			=> $products[$key]->getSku(),
						'name' 			=> $products[$key]->getName(),
						'price' 		=> $this->_helper->getPrice($products[$key]),
						'category' 		=> $category,
						'list' 			=> $category,
						'brand' 		=> $this->_helper->getBrand($products[$key]),
						'quantity' 		=> 1,
						'position' 		=> $position,
						'store' 		=> $this->_helper->getStoreName()
					];
				}
				
				/**
				 * Apply direct "Add to cart" tracking for listings
				 */
				if ('' !== $selector = $this->_helper->getListWidgetCartCategorySelector())
				{
					if (!in_array($products[$key]->getTypeId(),[\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE]))
					{
						foreach (@$query->query($selector, $element) as $a)
						{
							$click = $a->getAttribute('onclick');
							
							$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
							$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
							$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
							$a->setAttribute('data-category',   $category);
							$a->setAttribute('data-list',		$category);
							$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
							$a->setAttribute('data-quantity', 	1);
							$a->setAttribute('data-click',		$click);
							$a->setAttribute('data-position', 	$position);
							$a->setAttribute('data-store',		$this->_helper->getStoreName());
							$a->setAttribute('data-event',		'addToCart');
							
							if ($this->_helper->useClickHandler())
							{
								$a->setAttribute('onclick','return AEC.ajaxList(this,dataLayer)');
							}
							
							/**
							 * Create transport object
							 *
							 * @var \Magento\Framework\DataObject $transport
							 */
							$transport = new \Magento\Framework\DataObject
							(
								[
									'attributes' => $this->attributes->getAttributes()
								]
							);
							
							/**
							 * Notify others
							 */
							$this->_helper->getEventManager()->dispatch('ec_get_widget_add_list_attributes', ['transport' => $transport]);
							
							/**
							 * Get response
							 */
							$attributes = $transport->getAttributes();
							
							$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
						}
					}
				}
			}
			
			$position++;
		}
		
		$content = $this->getDOMContent($dom, $doc);
		
		$content .= $block->getLayout()->createBlock('Anowave\Ec\Block\Track')->setTemplate('widgets.phtml')->setData
		(
			[
				'impressions' => $this->_helper->getJsonHelper()->encode($impressions, JSON_PRETTY_PRINT)
			]
		)->toHtml();
		/**
		 * Save cache
		 */
		$this->cache->save($content, \Anowave\Ec\Model\Cache::CACHE_LISTING_WIDGET);
		
		return $content;
	}
	
	/**
	 * Modify Widget List widget
	 *
	 * @param \Magento\Catalog\Block\Product\Widget\NewWidget $block
	 * @param string $content
	 * @return string
	 */
	public function augmentWidgetListBlock(\Magento\CatalogWidget\Block\Product\ProductsList $block, $content)
	{
		/**
		 * Load cache
		 *
		 * @var string
		 */
		$cache = $this->cache->load(\Anowave\Ec\Model\Cache::CACHE_LISTING_PRODUCT_WIDGET . $block->getNameInLayout());
		
		if ($cache)
		{
			return $cache;
		}
		
		/**
		 * Retrieve list of impression product(s)
		 *
		 * @var array
		 */
		$products = [];
		
		$collection = $block->getProductCollection();
		
		if (!$collection)
		{
			return $content;
		}
		
		foreach ($collection as $product)
		{
			$products[] = $product;
		}
		
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		/**
		 * Default starting position
		 *
		 * @var integer $position
		 */
		$position = 1;
		
		/**
		 * Default category
		 *
		 * @var \Magento\Framework\Phrase $category
		 */
		$category = __($block->getTitle())->__toString();
		
		/**
		 * Impression push
		 *
		 * @var array $impressions
		 */
		$impressions =
		[
			'event' 	=> 'widgetViewNonInteractive',
			'ecommerce' =>
			[
				'currencyCode' 	=> $this->_helper->getCurrency(),
				'actionField'	=>
				[
					'list' => $category
				],
				'impressions' 	=> []
			]
		];
		
		foreach ($query->query($this->_helper->getListWidgetSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{
				foreach ($query->query($this->_helper->getListWidgetClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
					
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $category);
					$a->setAttribute('data-list',		$category);
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('data-click',$click);
					}
					
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-position',	$position);
					$a->setAttribute('data-event',		'productClick');
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('onclick','return AEC.click(this,dataLayer)');
					}
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_widget_click_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
					
					$impressions['ecommerce']['impressions'][] =
					[
						'id' 			=> $products[$key]->getSku(),
						'name' 			=> $products[$key]->getName(),
						'price' 		=> $this->_helper->getPrice($products[$key]),
						'category' 		=> $category,
						'list' 			=> $category,
						'brand' 		=> $this->_helper->getBrand($products[$key]),
						'quantity' 		=> 1,
						'position' 		=> $position,
						'store' 		=> $this->_helper->getStoreName()
					];
				}
				
				/**
				 * Apply direct "Add to cart" tracking for listings
				 */
				if ('' !== $selector = $this->_helper->getListWidgetCartCategorySelector())
				{
					if (!in_array($products[$key]->getTypeId(),[\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE]))
					{
						foreach (@$query->query($selector, $element) as $a)
						{
							$click = $a->getAttribute('onclick');
							
							$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
							$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
							$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
							$a->setAttribute('data-category',   $category);
							$a->setAttribute('data-list',		$category);
							$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
							$a->setAttribute('data-quantity', 	1);
							$a->setAttribute('data-click',		$click);
							$a->setAttribute('data-position', 	$position);
							$a->setAttribute('data-store',		$this->_helper->getStoreName());
							$a->setAttribute('data-event',		'addToCart');
							
							if ($this->_helper->useClickHandler())
							{
								$a->setAttribute('onclick','return AEC.ajaxList(this,dataLayer)');
							}
							
							/**
							 * Create transport object
							 *
							 * @var \Magento\Framework\DataObject $transport
							 */
							$transport = new \Magento\Framework\DataObject
							(
								[
									'attributes' => $this->attributes->getAttributes()
								]
								);
							
							/**
							 * Notify others
							 */
							$this->_helper->getEventManager()->dispatch('ec_get_widget_add_list_attributes', ['transport' => $transport]);
							
							/**
							 * Get response
							 */
							$attributes = $transport->getAttributes();
							
							$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
						}
					}
				}
			}
			
			$position++;
		}
		
		$content = $this->getDOMContent($dom, $doc);
		
		$content .= $block->getLayout()->createBlock('Anowave\Ec\Block\Track')->setTemplate('widgets.phtml')->setData
		(
			[
				'impressions' => $this->_helper->getJsonHelper()->encode($impressions, JSON_PRETTY_PRINT)
			]
		)->toHtml();
			
		/**
		 * Save cache
		 */
		$this->cache->save($content, \Anowave\Ec\Model\Cache::CACHE_LISTING_PRODUCT_WIDGET . $block->getNameInLayout());
		
		return $content;
	}
	
	/**
	 * Modify categories listing output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentListBlock($block, $content)
	{	
		/**
		 * Load cache
		 * 
		 * @var string
		 */
		$cache = $this->cache->load(\Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());

		if ($cache)
		{
			return $cache;
		}
		
		/**
		 * Retrieve list of impression product(s)
		 * 
		 * @var array
		 */
		$products = [];
		
		foreach ($block->getLoadedProductCollection() as $product)
		{
			$products[] = $product;
		}
		
		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

		$query = new \DOMXPath($dom);
		
		$position = 1;
		
		foreach ($query->query($this->_helper->getListSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{
				/**
				 * Get current category
				 *  
				 * @var object
				 */
				$category = $this->_coreRegistry->registry('current_category');

				/**
				 * Add data-* attributes used for tracking dynamic values
				 */
				foreach ($query->query($this->_helper->getListClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
						
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
					$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('data-click',$click);
					}
					
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-position',	$position);
					$a->setAttribute('data-event',		'productClick');
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('onclick','return AEC.click(this,dataLayer)');
					}
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_click_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
				}
				
				/**
				 * Apply direct "Add to cart" tracking for listings
				 */
				if ('' !== $selector = $this->_helper->getCartCategorySelector())
				{
					/**
					 * Skip tracking for configurable and grouped products from listings
					 */
					if (!in_array($products[$key]->getTypeId(), 
					[
						\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
						\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
					]))
					{
						foreach (@$query->query($selector, $element) as $a)
						{
							$click = $a->getAttribute('onclick');
						
							$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
							$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
							$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
							$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
							$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
							$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
							$a->setAttribute('data-quantity', 	1);
							$a->setAttribute('data-click',		$click);
							$a->setAttribute('data-position', 	$position);
							$a->setAttribute('data-store',		$this->_helper->getStoreName());
							$a->setAttribute('data-event',		'addToCart');
							
							if ($this->_helper->useClickHandler())
							{
								$a->setAttribute('onclick','return AEC.ajaxList(this,dataLayer)');
							}
							
							/**
							 * Create transport object
							 *
							 * @var \Magento\Framework\DataObject $transport
							 */
							$transport = new \Magento\Framework\DataObject
							(
								[
									'attributes' => $this->attributes->getAttributes()
								]
							);
							
							/**
							 * Notify others
							 */
							$this->_helper->getEventManager()->dispatch('ec_get_add_list_attributes', ['transport' => $transport]);
							
							/**
							 * Get response
							 */
							$attributes = $transport->getAttributes();
							
							$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
						}
					}
				}
			}
			
			$position++;
		}
		
		$content = $this->getDOMContent($dom, $doc);
		
		/**
		 * Save cache
		 */
		$this->cache->save($content, \Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());
		
		return $content;
	}
	
	/**
	 * Modify categories listing output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentListRelatedBlock($block, $content)
	{		
		/**
		 * Remove empty spaces
		 */
		$content = trim($content);
		
		if (!strlen($content))
		{
			return $content;
		}
		
		/**
		 * Load cache
		 *
		 * @var string
		 */
		$cache = $this->cache->load(\Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());
		
		if ($cache)
		{
			return $cache;
		}
		
		/**
		 * Retrieve list of impression product(s)
		 *
		 * @var array
		 */
		$products = [];
		
		if (null != $items = $this->bridge->getLoadedItems($block))
		{
			foreach ($items as $product)
			{
				$products[] = $product;
			}
		}

		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		$position = 1;
		
		foreach ($query->query($this->_helper->getListSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{
				/**
				 * Get all product categories
				 */
				$categories = $this->_helper->getCurrentStoreProductCategories($products[$key]);
				
				if (!$categories)
				{
					if (null !== $root = $this->_helper->getStoreRootDefaultCategoryId())
					{
						$categories[] = $root;
					}
				}
				
				if ($categories)
				{
					/**
					 * Load last category
					 */
					
					$category = $this->categoryRepository->get
					(
						end($categories)
					);
				}
				else 
				{
					$category = null;
				}

				/**
				 * Add data-* attributes used for tracking dynamic values
				 */
				foreach ($query->query($this->_helper->getListClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
					
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
					$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					$a->setAttribute('data-click',		$click);
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-position',	$position);
					$a->setAttribute('data-event',		'productClick');
					$a->setAttribute('data-block',		$block->getNameInLayout());
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('onclick','return AEC.click(this,dataLayer)');
					}
					
					if ($category)
					{
						$element->setAttribute('data-category', $this->_helper->getCategoryDetailList($products[$key], $category));
					}
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_click_list_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
				}

				/**
				 * Track "Add to cart" from Related products
				 */
				if ('' !== $selector = $this->_helper->getCartCategorySelector())
				{
					foreach (@$query->query($selector, $element) as $a)
					{
						$click = $a->getAttribute('onclick');
						
						$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
						$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
						$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
						$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
						$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
						$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
						$a->setAttribute('data-quantity', 	1);
						$a->setAttribute('data-click',		$click);
						$a->setAttribute('data-position', 	$position);
						$a->setAttribute('data-store',		$this->_helper->getStoreName());
						$a->setAttribute('data-event',		'addToCart');
						$a->setAttribute('data-block',		$block->getNameInLayout());
						
						if ($this->_helper->useClickHandler())
						{
							$a->setAttribute('onclick','return AEC.ajaxList(this,dataLayer)');
						}
						
						/**
						 * Create transport object
						 *
						 * @var \Magento\Framework\DataObject $transport
						 */
						$transport = new \Magento\Framework\DataObject
						(
							[
								'attributes' => $this->attributes->getAttributes()
							]
						);
						
						/**
						 * Notify others
						 */
						$this->_helper->getEventManager()->dispatch('ec_get_add_list_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
					}
				}
			}
			
			$position++;
		}
		
		$content = $this->getDOMContent($dom, $doc);
		
		/**
		 * Save cache
		 */
		$this->cache->save($content, \Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());
		
		return $content;
	}
	
	/**
	 * Modify categories listing output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentWishlistBlock($block, $content)
	{
		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		foreach ($query->query($this->_helper->getWishlistSelector()) as $key => $element)
		{
			$element->setAttribute('data-event','addToWishlist');
			
			if ($this->getCurrentProduct())
			{
				$element->setAttribute('data-event-label', $this->_helper->escapeDataArgument($this->getCurrentProduct()->getName()));
			}
		}
		
		return $this->getDOMContent($dom, $doc);
	}
	
	/**
	 * Modify wishlist sidebar
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentWishlistSidebarBlock($block, $content)
	{
		return $content;
	}
	
	/**
	 * Modify categories listing output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentCompareBlock($block, $content)
	{
		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		foreach ($query->query($this->_helper->getCompareSelector()) as $key => $element)
		{
			$element->setAttribute('data-event','addToCompare');
			
			if ($this->getCurrentProduct())
			{
				$element->setAttribute('data-event-label', $this->_helper->escapeDataArgument($this->getCurrentProduct()->getName()));
			}
		}
		
		return $this->getDOMContent($dom, $doc);
	}
	
	/**
	 * Modify categories listing output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 */
	protected function augmentListUpsellBlock($block, $content)
	{
		$content = trim($content);
		
		if (!strlen($content))
		{
			return $content;
		}
		
		/**
		 * Load cache
		 *
		 * @var string
		 */
		$cache = $this->cache->load(\Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());
		
		if ($cache)
		{
			return $cache;
		}
		
		/**
		 * Retrieve list of impression product(s)
		 *
		 * @var array
		 */
		$products = [];
		
		if ($block->getItems())
		{
			foreach ($block->getItems() as $product)
			{
				$products[] = $product;
			}
		}

		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$query = new \DOMXPath($dom);
		
		$position = 1;
		
		foreach ($query->query($this->_helper->getListSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{
				/**
				 * Get all product categories
				 */
				$categories = $this->_helper->getCurrentStoreProductCategories($products[$key]);
				
				if (!$categories)
				{
					if (null !== $root = $this->_helper->getStoreRootDefaultCategoryId())
					{
						$categories[] = $root;
					}
				}
				
				if ($categories)
				{
					/**
					 * Load last category
					 */
					$category = $this->categoryRepository->get
					(
						end($categories)
					);
				}
				else
				{
					$category = null;
				}
				
				/**
				 * Add data-* attributes used for tracking dynamic values
				 */
				foreach ($query->query($this->_helper->getListClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
					
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
					$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					$a->setAttribute('data-click',		$click);
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-position',	$position);
					$a->setAttribute('data-event',		'productClick');
					$a->setAttribute('data-block',		$block->getNameInLayout());
					
					if ($this->_helper->useClickHandler())
					{
						$a->setAttribute('onclick','return AEC.click(this,dataLayer)');
					}
					
					if ($category)
					{
						$element->setAttribute('data-category', $this->_helper->getCategoryDetailList($products[$key], $category));
					}
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_click_list_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
				}
				
				/**
				 * Track "Add to cart" from Related products
				 */
				if ('' !== $selector = $this->_helper->getCartCategorySelector())
				{
					foreach (@$query->query($selector, $element) as $a)
					{
						$click = $a->getAttribute('onclick');
						
						$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
						$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
						$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
						$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($category->getName()));
						$a->setAttribute('data-list',		$this->_helper->escapeDataArgument($this->_helper->getCategoryList($category)));
						$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
						$a->setAttribute('data-quantity', 	1);
						$a->setAttribute('data-click',		$click);
						$a->setAttribute('data-position', 	$position);
						$a->setAttribute('data-store',		$this->_helper->getStoreName());
						$a->setAttribute('data-event',		'addToCart');
						$a->setAttribute('data-block',		$block->getNameInLayout());
						
						if ($this->_helper->useClickHandler())
						{
							$a->setAttribute('onclick','return AEC.ajaxList(this,dataLayer)');
						}
						
						/**
						 * Create transport object
						 *
						 * @var \Magento\Framework\DataObject $transport
						 */
						$transport = new \Magento\Framework\DataObject
						(
							[
								'attributes' => $this->attributes->getAttributes()
							]
						);
						
						/**
						 * Notify others
						 */
						$this->_helper->getEventManager()->dispatch('ec_get_add_list_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
					}
				}
			}
			
			$position++;
		}
		
		$content = $this->getDOMContent($dom, $doc);
		
		/**
		 * Save cache
		 */
		$this->cache->save($content, \Anowave\Ec\Model\Cache::CACHE_LISTING . $block->getNameInLayout());
		
		return $content;
	}
	
	/**
	 * Modify remove from cart output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 * 
	 * @return string
	 */
	
	protected function augmentRemoveCartBlock($block, $content)
	{
		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		/**
		 * Modify DOM
		 */
		
		$x = new \DOMXPath($dom);
		
		foreach ($x->query($this->_helper->getDeleteSelector()) as $element)
		{
			/**
			 * Get all product categories
			 */
			$categories = $this->_helper->getCurrentStoreProductCategories($block->getItem()->getProduct());
			
			if (!$categories)
			{
				if (null !== $root = $this->_helper->getStoreRootDefaultCategoryId())
				{
					$categories[] = $root;
				}
			}
			
			if ($this->_helper->useClickHandler())
			{
				$element->setAttribute('onclick','return AEC.remove(this, dataLayer)');
			}
			
			if (!$this->_helper->useSimples())
			{
				$element->setAttribute('data-id', $this->_helper->escapeDataArgument($block->getItem()->getProduct()->getSku()));
			}
			else 
			{
				$element->setAttribute('data-id', $this->_helper->escapeDataArgument($block->getItem()->getSku()));
			}
			
			$element->setAttribute('data-name', 		  $this->_helper->escapeDataArgument($block->getItem()->getProduct()->getName()));
			$element->setAttribute('data-price', 		  $this->_helper->escapeDataArgument($this->_helper->getPrice($block->getItem()->getProduct())));
			$element->setAttribute('data-brand', 		  $this->_helper->escapeDataArgument($this->_helper->getBrand($block->getItem()->getProduct())));
			$element->setAttribute('data-quantity', (int) $block->getItem()->getQty());
			$element->setAttribute('data-event', 		  'removeFromCart');

			if ($element->getAttribute('data-post') && $this->_helper->getUseRemoveConfirm())
			{
				/**
				 * Get current data post
				 * 
				 * @var string $post
				 */
				$post = $element->getAttribute('data-post');
				
				/**
				 * Remove standard data-post
				 */
				$element->removeAttribute('data-post');
				
				/**
				 * Create new data-post
				 */
				$element->setAttribute('data-post-action', $post);
			}

			if ($categories)
			{
				/**
				 * Load last category
				 */
				$category = $this->categoryRepository->get
				(
					end($categories)
				);
				
				$element->setAttribute('data-category', 	$this->_helper->getCategoryDetailList($block->getItem()->getProduct(), $category));
				$element->setAttribute('data-list', 		$this->_helper->getCategoryDetailList($block->getItem()->getProduct(), $category));
			}
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'attributes' => $this->attributes->getAttributes()
				]
			);
			
			/**
			 * Notify others
			 */
			$this->_helper->getEventManager()->dispatch('ec_get_remove_attributes', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$attributes = $transport->getAttributes();
			
			$element->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
		}
		
		
		return $this->getDOMContent($dom, $doc);
	}
	
	
	/**
	 * Modify add to cart output
	 * 
	 * @param AbstractBlock $block
	 * @param string $content
	 * 
	 * @return string
	 */
	protected function augmentAddCartBlock($block, $content)
	{
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		
		$x = new \DOMXPath($dom);

		foreach ($x->query($this->_helper->getCartSelector()) as $element)
		{
			$category = $this->_coreRegistry->registry('current_category');
			
			if (!$category)
			{
				/**
				 * Get all product categories
				 */
				$categories = $this->_helper->getCurrentStoreProductCategories($block->getProduct());
				
				/**
				 * Cases when product does not exist in any category
				 */
				if (!$categories)
				{
					$categories[] = $this->_helper->getStoreRootDefaultCategoryId();
				}
					
				/**
				 * Load last category
				*/
				$category = $this->categoryRepository->get
				(
					end($categories)
				);
			}
			
			/**
			 * Get existing onclick attribute
			 * 
			 * @var string
			 */
			$click = $element->getAttribute('onclick');
			
			if ($this->_helper->useClickHandler())
			{
				$element->setAttribute('onclick','return AEC.ajax(this,dataLayer)');
			}
			
			$element->setAttribute('data-id', 			$this->_helper->escapeDataArgument($block->getProduct()->getSku()));
			$element->setAttribute('data-name', 		$this->_helper->escapeDataArgument($block->getProduct()->getName()));
			$element->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($block->getProduct())));
			$element->setAttribute('data-category', 	$this->_helper->escapeDataArgument($category->getName()));
			$element->setAttribute('data-list', 		$this->_helper->getCategoryDetailList($block->getProduct(), $category));
			$element->setAttribute('data-brand', 		$this->_helper->getBrand($block->getProduct()));
			$element->setAttribute('data-click', 		$click);
			
			try 
			{
				/**
				 * Get current stock level 
				 * 
				 * @var int $max
				 */
				$max = (int) $this->stockItemRepository->get($block->getProduct()->getId())->getQty();
				
				$element->setAttribute('data-quantity-max', $max);
			}
			catch (\Exception $e){}
			
			/**
			 * Set data event
			 */
			$element->setAttribute('data-event','addToCart');
			
			if ('grouped' == $block->getProduct()->getTypeId())
			{
				$element->setAttribute('data-grouped',1);
			}
			
			if ('configurable' == $block->getProduct()->getTypeId())
			{
				$element->setAttribute('data-configurable',1);
			}
			
			/**
			 * Set current store
			 */
			$element->setAttribute('data-currentstore', $this->_helper->getStoreName());
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'attributes' => $this->attributes->getAttributes()
				]
			);
			
			/**
			 * Notify others
			 */
			$this->_helper->getEventManager()->dispatch('ec_get_add_attributes', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$attributes = $transport->getAttributes();
			
			$element->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
		}

		return $this->getDOMContent($dom, $doc);
	}
	
	/**
	 * Retrieves body
	 *
	 * @param DOMDocument $dom
	 * @param DOMDocument $doc
	 * @param string $decode
	 */
	public function getDOMContent(\Anowave\Ec\Model\Dom $dom, \Anowave\Ec\Model\Dom $doc, $debug = false, $originalContent = '')
	{
		try
		{
			$head = $dom->getElementsByTagName('head')->item(0);
			$body = $dom->getElementsByTagName('body')->item(0);
			
			if ($head instanceof \DOMElement)
			{
				foreach ($head->childNodes as $child)
				{
					$doc->appendChild($doc->importNode($child, true));
				}
			}
		
			if ($body instanceof \DOMElement)
			{
				foreach ($body->childNodes as $child)
				{
					$doc->appendChild($doc->importNode($child, true));
				}
			}
		}
		catch (\Exception $e)
		{
			
		}

		$content = $doc->saveHTML();
		
		return html_entity_decode($content, ENT_COMPAT, 'UTF-8');
	}
	
	/**
	 * Get current product
	 */
	public function getCurrentProduct()
	{
		return $this->_coreRegistry->registry('current_product');
	}
	
	/**
	 * Accelerated Mobile Pages support
	 *
	 * @param string $content
	 * @return string
	 */
	public function augmentAmp($block, $content)
	{
		if (!$this->_helper->supportAmp())
		{
			return $content;
		}
	
		/**
		 * Parse content and detect amp-analytics snippet
		 */
		if (false !== strpos($content, 'amp-analytics'))
		{
			$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
			$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
				
			@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
				
			$x = new \DOMXPath($dom);
				
			$amp = $x->query('//amp-analytics');
	
			if ($amp->length > 0)
			{
				foreach ($amp as $node)
				{
					$params = $dom->createElement('script');
						
					$params->setAttribute('type','application/json');
						
					/**
					 * Enhanced Ecommerce parameters
					*/
					$params->nodeValue = $this->_helper->getJsonHelper()->encode($this->getAmpVariables($node, $block));
						
					$params = $node->appendChild($params);
				}
			}
			
			return $this->getDOMContent($dom, $doc);
		}
		
		return $content;
	}
	
	/**
	 * Generate AMP variables
	 *
	 * @param void
	 * @return []
	 */
	public function getAmpVariables(\DOMElement $node, $block)
	{
		$vars = [];
	
		/**
		 * Read pre-defined variables from static snippets and merge to global []
		*/
		foreach ($node->getElementsByTagName('script') as $script)
		{
			$vars = array_merge($vars, json_decode(trim($script->nodeValue), true));
		}
	
		/**
		 * Get visitor data
		 */
		$vars['vars']['visitor'] = json_decode($this->_helper->getVisitorPush($block), true);
		
		/**
		 * Read persistent dataLayer
		 */
		$data = $this->dataLayer->get();
		
		$vars['vars'] = array_merge_recursive($vars['vars'], $data);
		
		return $vars;
	}
}