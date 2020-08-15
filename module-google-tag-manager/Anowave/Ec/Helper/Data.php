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

use Magento\Store\Model\Store;
use Anowave\Package\Helper\Package;
use Magento\Framework\Registry;

class Data extends \Anowave\Package\Helper\Package
{
	/**
	 * Variant delimiter
	 *
	 * @var string
	 */
	const VARIANT_DELIMITER = '-';
	
	/**
	 * Variant attributes delimiter
	 *
	 * @var string
	 */
	const VARIANT_DELIMITER_ATT = ':';
	
	/**
	 * Asunc events
	 * 
	 * @var boolean
	 */
	const USE_ASYNC_EVENTS = false;
	
	/**
	 * Package name
	 * @var string
	 */
	protected $package = 'MAGE2-GTM';
	
	/**
	 * Config path 
	 * @var string
	 */
	protected $config = 'ec/general/license';
	
	/**
	 * Order products array 
	 * 
	 * @var array
	 */
	private $_orders = []; 
	
	/**
	 * Brand map (lazy load)
	 * 
	 * @var array
	 */
	private $_brandMap = [];
	
	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository = null;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * Customer session
	 * 
	 * @var \Magento\Customer\Model\Session $session
	 */
	protected $session = null;
	
	/**
	 * Group registry 
	 * 
	 * @var \Magento\Customer\Model\GroupRegistry
	 */
	protected $groupRegistry = null;
	
	/**
	 * Order collection factory 
	 * 
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $orderCollectionFactory = null;

	/**
	 * Order config
	 *
	 * @var \Magento\Sales\Model\Order\Config
	 */
	protected $orderConfig = null;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry = null;
	
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext = null;

	/**
	 * @var \Magento\Catalog\Helper\Data
	 */
	protected $catalogData = null;
	
	/**
	 * @var Magento\Customer\Model\Customer
	 */
	protected $customer = null;
	
	/**
	 * @var \Magento\Catalog\Model\Product\Attribute\Repository
	 */
	protected $productAttributeRepository = null;
	
	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
	 */
	protected $optionCollection;
	
	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $eavConfig;
	
	/**
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $eventManager = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Datalayer
	 */
	protected $dataLayer = null;
	
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager = null;
	
	/**
	 * @var \Magento\Framework\App\ProductMetadataInterface
	 */
	protected $productMetadata;
	
	/**
	 * @var \Magento\Framework\Module\ModuleListInterface
	 */
	protected $moduleList;
	
	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepositoryInterface;
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
	 */
	protected $attribute;
	
	/**
	 * @var \Anowave\Ec\Helper\Attributes
	 */
	protected $attributes;
	
	/**
	 * @var \Anowave\Ec\Helper\Bridge
	 */
	protected $bridge;
	
	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\PrivateData
	 */
	protected $privateData;
	
	/**
	 * @var \Magento\Catalog\Helper\Category
	 */
	protected $categoryHelper;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\Directive;
	 */
	protected $directive;
	
	/**
	 * @var \Anowave\Ec\Helper\Json
	 */
	protected $jsonHelper;
	
	/**
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $_salesOrderCollection;
	
	/**
	 * Check if returning customer
	 * 
	 * @var boolean
	 */
	private $returnCustomer = false;
	
	/**
	 * Current store categories 
	 * 
	 * @var array
	 */
	private $currentCategories = [];
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Magento\Customer\Model\Session $session
	 * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	 * @param \Magento\Sales\Model\Order\Config $orderConfig
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Magento\Catalog\Helper\Data $catalogData
	 * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
	 * @param \Magento\Eav\Model\Config $eavConfig
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
	 * @param \Magento\Framework\Module\ModuleListInterface $moduleList
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
	 * @param \Anowave\Ec\Helper\Attributes $attributes
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
	 * @param \Anowave\Ec\Helper\Bridge $bridge
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	 * @param \Anowave\Ec\Model\Cookie\PrivateData $privateData
	 * @param \Magento\Catalog\Helper\Category $categoryHelper
	 * @param \Anowave\Ec\Model\Cookie\Directive $directive
	 * @param \Anowave\Ec\Helper\Json $jsonHelper
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context, 
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Customer\Model\Session $session,
		\Magento\Customer\Model\GroupRegistry $groupRegistry,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Catalog\Helper\Data $catalogData,
		\Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection,
		\Magento\Eav\Model\Config $eavConfig,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata,
		\Magento\Framework\Module\ModuleListInterface $moduleList,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Anowave\Ec\Helper\Attributes $attributes,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
		\Anowave\Ec\Helper\Bridge $bridge,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Anowave\Ec\Model\Cookie\PrivateData $privateData,
		\Magento\Catalog\Helper\Category $categoryHelper,
		\Anowave\Ec\Model\Cookie\Directive $directive,
		\Anowave\Ec\Helper\Json $jsonHelper,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection, 
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry
		 */
		$this->registry = $registry;
		
		/**
		 * Set product repository
		 * 
		 * @var \Magento\Catalog\Api\ProductRepositoryInterface
		 */
		$this->productRepository = $productRepository;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set Group Registry 
		 * 
		 * @var \Magento\Customer\Model\GroupRegistry
		 */
		$this->groupRegistry = $groupRegistry;
		
		/**
		 * Set session
		 * 
		 * @var \Magento\Customer\Model\Session $session
		 */
		$this->session = $session;
		
		/**
		 * Set order collection factory 
		 * 
		 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
		 */
		$this->orderCollectionFactory = $orderCollectionFactory;
		
		/**
		 * Set order config 
		 * 
		 * @var \Magento\Sales\Model\Order\Config
		 */
		$this->orderConfig = $orderConfig;
		
		/**
		 * Set context 
		 * 
		 * @var \Magento\Framework\App\Http\Context
		 */
		$this->httpContext = $httpContext;
		
		/**
		 * Set catalog data 
		 * 
		 * @var \Magento\Catalog\Helper\Data
		 */
		$this->catalogData = $catalogData;
		
		/**
		 * Set attribute repository 
		 * 
		 * @var \Magento\Catalog\Model\Product\Attribute\Repository
		 */
		$this->productAttributeRepository = $productAttributeRepository;
		
		/**
		 * Set option collection
		 * 
		 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
		 */
		$this->optionCollection = $optionCollection;
		
		/**
		 * Default collection filter(s) and sorting
		 */
		$this->optionCollection->setPositionOrder('asc')->setStoreFilter(0);
		
		/**
		 * Set scope config 
		 * 
		 * @var \Magento\Framework\App\Config\ScopeConfigInterface
		 */
		$this->scopeConfig = $context->getScopeConfig();
		
		/**
		 * Set event manager 
		 * 
		 * @var \Magento\Framework\Event\ManagerInterface
		 */
		$this->eventManager = $context->getEventManager();
		
		/**
		 * Set dataLayer 
		 * 
		 * @var \Anowave\Ec\Helper\Datalayer
		 */
		$this->dataLayer = $dataLayer;
		
		/**
		 * Set eav config 
		 * 
		 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
		 */
		$this->eavConfig = $eavConfig;
		
		/**
		 * Set Store Manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set meta data 
		 * 
		 * @var \Magento\Framework\App\ProductMetadataInterface $productMetadata
		 */
		$this->productMetadata = $productMetadata;
		
		/**
		 * Set module list 
		 * 
		 * @var \Magento\Framework\Module\ModuleListInterface $moduleList
		 */
		$this->moduleList = $moduleList;
		
		/**
		 * Set customer repository interface
		 * 
		 * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
		 */
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		
		/**
		 * Set attributes
		 * 
		 * @var \Anowave\Ec\Helper\Attributes $attributes
		 */
		$this->attributes = $attributes;
		
		/**
		 * Set attribute 
		 * 
		 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
		 */
		$this->attribute = $attribute;
		
		/**
		 * Set bridge 
		 * 
		 * @var \Anowave\Ec\Helper\Data $bridge
		 */
		$this->bridge = $bridge;
		
		/**
		 * Set RedirectInterface
		 * 
		 * @var \Magento\Framework\App\Response\RedirectInterface $redirect
		 */
		$this->redirect = $redirect;
		
		/**
		 * Set private data
		 * 
		 * @var \Anowave\Ec\Model\Cookie\PrivateData $privateData
		 */
		$this->privateData = $privateData;
		
		/**
		 * Set category helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $categoryHelper
		 */
		$this->categoryHelper = $categoryHelper;
		
		/**
		 * Set cookie directive 
		 * 
		 * @var \Anowave\Ec\Helper\Data $directive
		 */
		$this->directive = $directive;
		
		/**
		 * Set JSON helper 
		 * 
		 * @var \Anowave\Ec\Helper\Json $jsonHeler
		 */
		$this->jsonHelper = $jsonHelper;
		
		/**
		 * Set order sales collection
		 * 
		 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_salesOrderCollection
		 */
		$this->_salesOrderCollection = $salesOrderCollection;
	}
	
	/**
	 * Get order revenue
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return number|number|NULL
	 */
	public function getRevenue(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		switch ((int) $this->getConfig('ec/tax/revenue'))
		{
			case \Anowave\Ec\Model\System\Config\Source\Tax::INCL_TAX: return $order->getGrandTotal();
			case \Anowave\Ec\Model\System\Config\Source\Tax::EXCL_TAX: return $order->getSubtotal();
		}
		
		/**
		 * By default return incl. tax
		 */
		return $order->getGrandTotal();
	}
	
	/**
	 * Get item price 
	 * 
	 * @param \Magento\Sales\Model\Order\Item $item
	 * @return number|NULL
	 */
	public function getRevenueProduct(\Magento\Sales\Model\Order\Item $item)
	{
		switch ((int) $this->getConfig('ec/tax/revenue_product'))
		{
			case \Anowave\Ec\Model\System\Config\Source\TaxItem::INCL_TAX: return $item->getPriceInclTax();
			case \Anowave\Ec\Model\System\Config\Source\TaxItem::EXCL_TAX: return $item->getPrice();
		}
		
		return $item->getPrice();
	}
	
	/**
	 * Get AdWords ecomm_totalvalue
	 *
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return number|number|NULL
	 */
	public function getRevenueAdWords(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		switch ((int) $this->getConfig('ec/tax/ecomm_totalvalue'))
		{
			case \Anowave\Ec\Model\System\Config\Source\Tax::INCL_TAX: return $order->getGrandTotal();
			case \Anowave\Ec\Model\System\Config\Source\Tax::EXCL_TAX: return $order->getSubtotal();
		}
		
		/**
		 * By default return incl. tax
		 */
		return $order->getGrandTotal();
	}

	/**
	 * Get checkout push 
	 * 
	 * @param unknown $block
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCheckoutPush($block, \Magento\Checkout\Model\Cart $cart, \Magento\Framework\Registry $registry)
	{
		/**
		 * Get data 
		 * 
		 * @var StdClass $data
		 */
		$data = $this->getCheckoutProducts($block, $cart, $registry);
		
		/**
		 * Update total value
		 */
		$data->google_tag_params->ecomm_totalvalue = (float) $cart->getQuote()->getGrandTotal();
		
		/**
		 * Return
		 */
		return $this->getJsonHelper()->encode
		(
			[
				'push' => 
				[
					'event' => 'checkout',
					'ecommerce' =>
					[
						'currencyCode' 	=> $this->getStore()->getCurrentCurrencyCode(),
						'checkout' =>
						[
							'actionField' =>
							[
								'step' => \Anowave\Ec\Helper\Constants::CHECKOUT_STEP_SHIPPING
							],
							'products' => $data->products
						]
					]
				],
				'google_tag_params' => $data->google_tag_params,
				'total'	=> (float) $cart->getQuote()->getGrandTotal()
				
			],
			JSON_PRETTY_PRINT
		);
	}
	
	/**
	 * Get cart push 
	 * 
	 * @param unknown $block
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCartPush($block, \Magento\Checkout\Model\Cart $cart, \Magento\Framework\Registry $registry)
	{
		/**
		 * Get data
		 *
		 * @var StdClass $data
		 */
		$data = $this->getCheckoutProducts($block, $cart, $registry);
		
		/**
		 * Update total value
		 */
		$data->google_tag_params->ecomm_totalvalue = (float) $cart->getQuote()->getGrandTotal();
		
		/**
		 * Return
		 */
		return $this->getJsonHelper()->encode($data);
	}
	
	/**
	 * Get checkout products 
	 * 
	 * @param unknown $block
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCheckoutProducts($block, \Magento\Checkout\Model\Cart $cart, \Magento\Framework\Registry $registry)
	{
		/**
		 * Products 
		 * 
		 * @var array $products
		 */
		$products = [];
		
		/**
		 * AdWords Dynamic Remarketing parameters 
		 * 
		 * @var StdClass $google_tag_params
		 */
		$google_tag_params = (object)
		[
			'ecomm_prodid' 			=> [],
			'ecomm_pvalue' 			=> [],
			'ecomm_pname' 			=> [],
			'ecomm_totalvalue' 		=> 0
		];
		
		foreach ($cart->getQuote()->getAllVisibleItems() as $item)
		{
			/**
			 * Get all product categories
			 */
			$categories = $this->getCurrentStoreProductCategories($item->getProduct());
			
			if (!$categories)
			{
				/**
				 * Load product by id
				 */
				$categories = $this->getCurrentStoreProductCategories
				(
					$this->productRepository->getById
					(
						$item->getProduct()->getId()
					)
				);
			}
			
			/**
			 * Cases when product does not exist in any category
			 */
			if (!$categories)
			{
				$categories[] = $this->getStoreRootDefaultCategoryId();
			}
			
			/**
			 * Load last category 
			 */
			$category = $this->categoryRepository->get
			(
				end($categories)
			);
			
			/**
			 * Variant
			 * 
			 * @var array $variant
			 */
			$variant = [];
			
			$data = new \Magento\Framework\DataObject(array
			(
				'id' 		=> 		 	$item->getSku(),
				'name' 		=> 		 	$item->getName(),
				'price' 	=> (float)  $item->getPriceInclTax(),
				'quantity' 	=> (int) 	$item->getQty(),
				'category'	=> 		 	$this->getCategory($category),
				'brand'		=> 		 	$this->getBrand
				(
					$item->getProduct()
				)
			));

			/**
			 * AdWords Dynamic Remarketing 
			 * 
			 * @var \Magento\Framework\DataObject $ecomm
			 */
			$ecomm = new \Magento\Framework\DataObject(array
			(
				'id' 		=> 		 	$this->getAdwordsEcommProdId($item),
				'name' 		=> 		 	$data->getName(),
				'price' 	=> (float)  $data->getPrice(),
			));
			
			if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
			{
				$variant = [];

				/**
				 * Get buy request 
				 * 
				 * @var []
				 */
				$buyRequest = $item->getProductOptionByCode('info_buyRequest');
				
				/**
				 * Check if buy request is set
				 */
				if ($buyRequest)
				{
					/**
					 * Get info buy request
					 *
					 * @var \Magento\Framework\DataObject
					 */
					$info = new \Magento\Framework\DataObject($buyRequest);
				}
				else 
				{
					/**
					 * Try to obtain buy request as custom option
					 * 
					 * @var []
					 */
					$buyRequest = $item->getProduct()->getCustomOption('info_buyRequest');
					
					if (isset($buyRequest['value']))
					{
						if (false === $value = @unserialize($buyRequest['value']))
						{
							$value = @json_decode($buyRequest['value'], true);
						}
						
						if ($value)
						{
							$info = new \Magento\Framework\DataObject($value);
						}
					}
					else
					{
						$info = new \Magento\Framework\DataObject([]);
					}
				}

				if (isset($info) && $info->getSuperAttribute())
				{
					/**
					 * Construct variant
					 */
					foreach ((array) $info->getSuperAttribute() as $id => $option)
					{
						$attribute = $this->attribute->create()->load($id);
						
						if ($attribute->usesSource())
						{
							$name = $this->getAttributeLabel($attribute);
							$text = $attribute->getSource()->getOptionText($option);
							
							if ($this->useDefaultValues())
							{
								/**
								 * Get current store
								 * 
								 * @var int
								 */
								$currentStore = $attribute->getSource()->getAttribute()->getStoreId();
								
								/**
								 * Change default store
								 */
								$attribute->getSource()->getAttribute()->setStoreId(0);
								
								/**
								 * Get text
								 * 
								 * @var string
								 */
								$text = $attribute->getSource()->getOptionText($option);
								
								/**
								 * Restore store
								 */
								$attribute->getSource()->getAttribute()->setStoreId($currentStore);
							}
							
							$variant[] = join(self::VARIANT_DELIMITER_ATT, array($name, $text));
	
						}
					}
				}
			
				if (!$this->useSimples())
				{
					$data->setId
					(
						$item->getProduct()->getSku()
					);
						
					$data->setName
					(
						$item->getProduct()->getName()
					);
					
					$ecomm->setId
					(
						$this->getAdwordsEcommProdId
						(
							$item->getProduct()
						)
					);
					
					$ecomm->setName
					(
						$item->getProduct()->getName()
					);
				}
				
				/**
				 * Load configurable
				 */
				$configurable = $this->productRepository->getById
				(
					$item->getProductId()
				);
			
				if (!$this->useSimples())
				{
					$data->setId
					(
						$configurable->getSku()
					);
					
					$data->setName
					(
						$configurable->getName()
					);
					
					$ecomm->setId
					(
						$this->getAdwordsEcommProdId($configurable)
					);
					
					$ecomm->setName
					(
						$configurable->getName()
					);
				}
				else 
				{
					if ($option = $item->getOptionByCode('simple_product')) 
					{
						$data->setId
						(
							$this->getAdwordsEcommProdId($option->getProduct())
						);
						
						$data->setName
						(
							$option->getProduct()->getName()
						);
						
						$ecomm->setId
						(
							$this->getAdwordsEcommProdId($option->getProduct())
						);
						
						$ecomm->setName
						(
							$option->getProduct()->getName()
						);
					}
				}
				
				$data->setBrand
				(
					$this->getBrand($configurable)
				);
					
				/**
				 * Push variant to data
				 *
				 * @var array
				 */
				$data->setVariant(join(self::VARIANT_DELIMITER, $variant));
			}
			
			/**
			 * Add product
			 */
			$products[] = $data->getData();
			
			/**
			 * AdWords Dynamic Remarketing
			 */
			$google_tag_params->ecomm_prodid[] = $ecomm->getId();
			$google_tag_params->ecomm_pvalue[] = $ecomm->getPrice();
			$google_tag_params->ecomm_pname[]  = $ecomm->getName();
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
		$this->eventManager->dispatch('ec_get_checkout_attributes', ['transport' => $transport]);
		
		/**
		 * Get response
		 */
		$attributes = $transport->getAttributes();
		
		foreach ($products as &$product)
		{
			foreach ($attributes as $key => $value)
			{
				$product[$key] = $value;
			}
		}
		
		unset($product);
		
		return (object)
		[
			'products' 			=> $products,
			'google_tag_params' => $google_tag_params
		];
	}
	
	/**
	 * Impressions push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 */
	public function getImpressionPushForward($block)
	{
		if (!$this->registry->registry('current_category') || 'category' !== $this->request->getControllerName())
		{
			return false;	
		}
		
		try 
		{
			$list = $block->getLayout()->getBlock('category.products.list');
			
			if ($list)
			{
				$category = $this->registry->registry('current_category');
				
				$response = 
				[
					'ecommerce' => 
					[
						'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
						'actionField' => 
						[
							'list' => $this->getCategoryList($category)
						],
						'impressions' => []
					]
				];
				
				/**
				 * Get loaded collection 
				 * 
				 * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
				 */
				$collection = $this->getLoadedCollection($list);
				
				/**
				 * Set default position
				 * 
				 * @var integer $position
				 */
				$position = 1;
				
				/**
				 * Consider pagination
				 * 
				 * @var int $p
				 */
				$p = (int) $collection->getCurPage();
				
				if ($p > 1)
				{
					$position += (($p-1) * (int) $collection->getPageSize());
				}
				
				/**
				 * Push data 
				 * 
				 * @var []
				 */
				$data = [];
				
				$taxonomy = (object) 
				[
					'list' => $this->getCategoryList($category),
					'name' => $this->getCategory($category)
				];
				
				foreach ($collection as $product)
				{
					$entity = 
					[
						'list' 			=> $taxonomy->list,
						'category'		=> $taxonomy->name,
						'id'			=> $product->getSku(),
						'name'			=> $product->getName(),
						'brand'			=> $this->getBrand
						(
							$product
						),
						'price'			=> $this->getPrice($product),
						'position'		=> $position++
					];
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes(), 
							'entity'	 => $entity
						]
					);
					
					/**
					 * Notify others
					 */
					$this->eventManager->dispatch('ec_get_impression_item_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					/**
					 * Add entity to impression array
					 */
					$response['ecommerce']['impressions'][] = array_merge($entity, $attributes);	
				}
				
				$response['currentStore'] = $this->getStoreName();
			}
			else 
			{
				/**
				 * @todo: Handle non-existent block
				 */
			}

			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'response' => $response
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_impression_data_after', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$response = $transport->getResponse();
			
			/**
			 * Facebook data
			 *
			 * @var []
			 */
			
			$content_name =  $taxonomy->name;
			
			$fbq = 
			[
				'content_name'		=> $content_name,
				'content_category' 	=> $content_name,
				'content_type' 		=> 'product',
				'content_ids' 		=> array_map
				(
					function($entity) {return $entity['id']; }, $response['ecommerce']['impressions']
				)
 			];

			return (object) 
			[
				'push' 				=> $this->getJsonHelper()->encode($response),
				'google_tag_params' => array
				(
					'ecomm_pagetype' => 'category',
					'ecomm_category' => $this->escape($taxonomy->name)
				),
				'fbq' => $this->getJsonHelper()->encode($fbq)
			];
		}
		catch (\Exception $e)
		{
			
		}
		
		return false;
	}

	/**
	 * Get loaded product collection from product list block 
	 *  
	 * @param \Magento\Catalog\Block\Product\ListProduct $list
	 */
	protected function getLoadedCollection(\Magento\Catalog\Block\Product\ListProduct $list)
	{
		$collection = $list->getLoadedProductCollection();
		
		/**
		 * Get toolbar
		 */
		$toolbar = $list->getToolbarBlock();
		
		if ($toolbar)
		{
			$orders = $list->getAvailableOrders();
			
			if ($orders) 
			{
				$toolbar->setAvailableOrders($orders);
			}
			
			$sort = $list->getSortBy();
			
			if ($sort) 
			{
				$toolbar->setDefaultOrder($sort);
			}
			
			$dir = $list->getDefaultDirection();
			
			if ($dir) 
			{
				$toolbar->setDefaultDirection($dir);
			}
			
			$modes = $list->getModes();
			
			if ($modes)
			{
				$toolbar->setModes($modes);
			}
			
			$collection->setCurPage($toolbar->getCurrentPage());

			$limit = (int) $toolbar->getLimit();
			
			if ($limit) 
			{
				$collection->setPageSize($limit);
			}
			
			if ($toolbar->getCurrentOrder()) 
			{
				$collection->setOrder($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());
			}
		}
		
		return $collection;
	}
	
	/**
	 * Get detail push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass|boolean
	 */
	public function getDetailPushForward($block)
	{
		$info = $block->getLayout()->getBlock('product.info');
		
		if ($info)
		{
			$category = $this->registry->registry('current_category');
			
			if (!$category)
			{
				/**
				 * Filter current categories only
				 */
				$categories = $this->getCurrentStoreProductCategories($info->getProduct());
				
				/**
				 * Cases when product does not exist in any category
				 */
				if (!$categories)
				{
					$categories[] = $this->getStoreRootDefaultCategoryId();
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
			$this->eventManager->dispatch('ec_get_detail_attributes', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$attributes = $transport->getAttributes();
			
			$data = 
			[
				'ecommerce' => 
				[
					'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
					'detail' => 
					[
						'products' => 
						[
							array_merge
							(
								[
									'id' 		=> $info->getProduct()->getSku(),
									'name' 		=> $info->getProduct()->getName(),
									'price' 	=> $this->getPrice($info->getProduct()),
									'brand'		=> $this->getBrand
									(
										$info->getProduct()
									),
									'category'	=> $this->getCategory($category),
									'quantity' 	=> 1
								], 
								$attributes
							)
						]
					]
				]
			];
			
			/**
			 * There is discrepancy between Google's specification with regards to whether 'list' parameter should be used in 'detail' JSON (
			 * 
			 * @see https://developers.google.com/tag-manager/enhanced-ecommerce#details
			 * @see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-detail-view 
			 * 
			 * @todo Pending feedback from Google
			 */
			if (1 === (int) $this->getConfig('ec/options/use_detail_list'))
			{
				$data['ecommerce']['detail']['actionField']['list'] = $this->getCategoryList($category);
			}
			
			
			$data['currentStore'] = $this->getStoreName();
			
			/**
			 * Persist data in dataLayer
			 */
			$this->dataLayer->merge($data);
			
			/**
			 * Prepare Related & Upsells impressions
			 */
			$data['ecommerce']['impressions'] = [];
			
			/**
			 * Related
			 */
			try 
			{
				$list = $block->getLayout()->getBlock('catalog.product.related');
				
				if ($list)
				{
					/**
					 * Set default position
					 *
					 * @var integer $position
					 */
					$position = 1;

					/**
					 * Push data
					 *
					 * @var []
					 */
					
					foreach ($this->bridge->getLoadedItems($list) as $product)
					{
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
						$this->eventManager->dispatch('ec_get_impression_related_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$entity = array_merge
						(
							[
								'list' 			=> \Anowave\Ec\Helper\Constants::LIST_RELATED,
								'category'		=> \Anowave\Ec\Helper\Constants::LIST_RELATED,
								'id'			=> $product->getSku(),
								'name'			=> $product->getName(),
								'brand'			=> $this->getBrand
								(
									$product
								),
								'price'			=> $this->getPrice($product),
								'position'		=> $position++
							], 
							$attributes
						);
						
						$data['ecommerce']['impressions'][] = $entity;
					}
				}
			}
			catch (\Exception $e){}
			
			/**
			 * Upsells
			 */
			try 
			{
				$list = $block->getLayout()->getBlock('product.info.upsell');
				
				if ($list)
				{
					/**
					 * Set default position
					 *
					 * @var integer $position
					 */
					$position = 1;
					
					/**
					 * Push data
					 *
					 * @var []
					 */

					foreach ($this->bridge->getLoadedItems($block) as $product)
					{
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
						$this->eventManager->dispatch('ec_get_impression_upsell_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$entity = array_merge
						(
							[
								'list' 			=> \Anowave\Ec\Helper\Constants::LIST_UP_SELL,
								'category'		=> \Anowave\Ec\Helper\Constants::LIST_UP_SELL,
								'id'			=> $product->getSku(),
								'name'			=> $product->getName(),
								'brand'			=> $this->getBrand
								(
									$product
									),
								'price'			=> $this->getPrice($product),
								'position'		=> $position++
							], 
							$attributes
						);
						
						$data['ecommerce']['impressions'][] = $entity;
					}
				}
				
				
			}
			catch (\Exception $e){}
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'response' => $data
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_detail_data_after', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$data = $transport->getResponse();
			
			/**
			 * Return
			 */
			return (object) 
			[
				'push' 				=> $this->getJsonHelper()->encode($data),
				'fbq'				=> $this->getJsonHelper()->encode($this->getFacebookViewContentTrack($info->getProduct(), $category)),
				'google_tag_params' => 
				[
					'ecomm_pagetype' 	=> 		   'product',
					'ecomm_category'	=> 		   $this->escape($this->getCategory($category)),
					'ecomm_prodid'		=> 		   $this->escape
					(
						$this->getAdwordsEcommProdId($info->getProduct())
					),
					'ecomm_totalvalue'	=> (float) $this->getPrice($info->getProduct())
				],
				'group' => $this->getDetailGroup($info, $category)
			];
		}
		
		return false;
	}

	/**
	 * Get grouped products
	 * 
	 * @param unknown $block
	 * @param unknown $category
	 * @return string
	 */
	public function getDetailGroup(\Magento\Catalog\Block\Product\View $block, \Magento\Catalog\Model\Category $category)
	{
		$group = [];
		
		if (\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE == $block->getProduct()->getTypeId())
		{
			foreach ($block->getProduct()->getTypeInstance(true)->getAssociatedProducts($block->getProduct()) as $product)
			{
				$group[] = 
				[
					'id' 		=> $product->getId(),
					'sku'		=> $product->getSku(),
					'name' 		=> $product->getName(),
					'price' 	=> $this->getPrice($product),
					'brand'		=> $this->getBrand($product),
					'category'	=> $this->getCategory($category)
				];
			}
		}
		
		return $this->getJsonHelper()->encode($group);
	}
	
	/**
	 * Purchase push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return string
	 */
	public function getPurchasePush($block)
	{
		$products = [];
		$response = [];
		
		foreach ($this->getOrders($block) as $order)
		{
			$response = 
			[
				'ecommerce' => 
				[
					'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
					'purchase' 	   => 
					[
						'actionField' => 
						[
							'id' 			=> 		    $order->getIncrementId(),
							'revenue' 		=> 	(float) $this->getRevenue($order),
							'tax'			=> 	(float) $order->getTaxAmount(),
							'shipping' 		=> 	(float) $order->getShippingAmount(),
							'coupon'		=> (string) $order->getCouponCode(),
							'affiliation' 	=> (string) $this->getStore()->getName()
						],
						'products' => []
					]
				],
				'facebook' => 
				[
					'revenue' 	=> (float) $order->getGrandTotal(),
					'subtotal' 	=> (float) $order->getSubtotal()
				],
				'payment' => 
				[
					'method' => $order->getPayment()->getMethodInstance()->getTitle()
				],
				'shipping' => 
				[
					'method' => $order->getShippingDescription()
				]
			];

			if ($order->getIsVirtual())
			{
				$address = $order->getBillingAddress();
			}
			else
			{
				$address = $order->getShippingAddress();
			}

			foreach ($order->getAllVisibleItems() as $item)
			{
				$variant = [];
				
				$category = $this->registry->registry('current_category');
				
				if (!$category)
				{
					/**
					 * Get all product categories
					 */
					$categories = $this->getCurrentStoreProductCategories($item->getProduct());
					
					/**
					 * Cases when product does not exist in any category
					 */
					if (!$categories)
					{
						$categories[] = $this->getStoreRootDefaultCategoryId();
					}
					
					/**
					 * Load last category
					*/
					$category = $this->categoryRepository->get
					(
						end($categories)
					);
				}
				
				$data = new \Magento\Framework\DataObject(
				[
					'id' 		=> 		 	$item->getSku(),
					'name' 		=> 		 	$item->getName(),
					'price' 	=> (float) 	$this->getRevenueProduct($item),
					'quantity' 	=> (int) 	$item->getQtyOrdered(),
					'category'	=> 		 	$this->getCategory($category),
					'brand'		=> 		 	$this->getBrand
					(
						$item->getProduct()
					)
				]);

				if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
				{
					$variant = [];

					/**
					 * Get buy request 
					 * 
					 * @var []
					 */
					$buyRequest = $item->getProductOptionByCode('info_buyRequest');
					
					/**
					 * Check if buy request is set
					 */
					if ($buyRequest)
					{
						/**
						 * Get info buy request
						 *
						 * @var \Magento\Framework\DataObject
						 */
						$info = new \Magento\Framework\DataObject($buyRequest);
					}
					else 
					{
						/**
						 * Try to obtain buy request as custom option
						 * 
						 * @var []
						 */
						$buyRequest = $item->getProduct()->getCustomOption('info_buyRequest');
						
						if (isset($buyRequest['value']))
						{
							$value = unserialize($buyRequest['value']);
								
							$info = new \Magento\Framework\DataObject($value);
						}
						else
						{
							$info = new \Magento\Framework\DataObject([]);
						}
					}
	
            		/**
            		 * Construct variant
            		 */
					foreach ($info->getSuperAttribute() as $id => $option)
					{
						/**
						 * Load attribute
						 * 
						 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
						 */
						$attribute = $this->attribute->create()->load($id);
						
						if ($attribute->usesSource())
						{
							$name = $this->getAttributeLabel($attribute);
							$text = $attribute->getSource()->getOptionText($option);
							
							if ($this->useDefaultValues())
							{
								/**
								 * Get current store
								 *
								 * @var int
								 */
								$currentStore = $attribute->getSource()->getAttribute()->getStoreId();
									
								/**
								 * Change default store
								*/
								$attribute->getSource()->getAttribute()->setStoreId(0);
									
								/**
								 * Get text
								 *
								 * @var string
								*/
								$text = $attribute->getSource()->getOptionText($option);
									
								/**
								 * Restore store
								*/
								$attribute->getSource()->getAttribute()->setStoreId($currentStore);
							}
							
							$variant[] = join(self::VARIANT_DELIMITER_ATT, array($name, $text));
						}
					}
					
					if (!$this->useSimples())
					{
						$data->setId
						(
							$item->getProduct()->getSku()
						);
						
						$data->setName
						(
							$item->getProduct()->getName()
						);
					}
					else 
					{
						if($item->getHasChildren())
						{
							foreach($item->getChildrenItems() as $child)
							{
								$data->setName
								(
									$child->getName()
								);
							}
						}
					}
					
					/**
					 * Push variant to data
					 *
					 * @var array
					*/
					$data->setVariant(join(self::VARIANT_DELIMITER, $variant));
				}
				
				/**
				 * Track product specific coupon
				 */
				
				if (null !== $item->getAppliedRuleIds())
				{
					/**
					 * Get applied rules
					 * 
					 * @var array $rules
					 */
					$rules = explode(chr(44), (string) $item->getAppliedRuleIds());

					/**
					 * Add coupon parameter if any applied rules. 
					 * 
					 * By default Magento 2.x does not support multiple coupon codes applied at once so order coupon code should match product coupon code
					 * 
					 * @todo Implement multiple coupon codes supported by 3rd parties
					 */
					if ($rules)
					{
						$data->setCoupon((string) $order->getCouponCode());
					}
				}
				
				/**
				 * Create transport object
				 *
				 * @var \Magento\Framework\DataObject $transport
				 */
				$transport = new \Magento\Framework\DataObject
				(
					[
						'product' 	 => $data,
						'quote_item' => $item
					]
				);
				
				/**
				 * Notify others
				 */
				$this->eventManager->dispatch('ec_order_products_product_get_after', ['transport' => $transport]);
				
				/**
				 * Get product
				 */
				$data = $transport->getProduct();
			
				/**
				 * Add product
				 */
				$products[] = $data->getData();
			}
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'products' => $products
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_order_products_get_after', ['transport' => $transport]);
			
			/**
			 * Get products
			 */
			$products = $transport->getProducts();
			
			/**
			 * Set products
			 */
			$response['ecommerce']['purchase']['products'] = $products;
			
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
			$this->eventManager->dispatch('ec_get_purchase_attributes', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$attributes = $transport->getAttributes();
			
			foreach ($response['ecommerce']['purchase']['products'] as &$product)
			{
				foreach ($attributes as $key => $value)
				{
					$product[$key] = $value;
				}
			}
			
			unset($product);
			
			$response['currentStore'] = $this->getStoreName();
			
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'response' => $response
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_purchase_push_after', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$response = $transport->getResponse();
		}

		return $this->getJsonHelper()->encode($response);
	}
	
	/**
	 * Get purchase google tag params 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass
	 */
	public function getPurchaseGoogleTagParams($block)
	{
		$google_tag_params = (object) 
		[
			'ecomm_prodid' 			=> [],
			'ecomm_pvalue' 			=> [],
			'ecomm_pname' 			=> [],
			'ecomm_totalvalue' 		=> 0
		];
		
		foreach ($this->getOrders($block) as $order)
		{
			foreach ($order->getAllVisibleItems() as $item)
			{
				$data = new \Magento\Framework\DataObject(array
				(
					'id'  			=> $this->escape($item->getSku()),
					'name' 			=> $this->escape($item->getName()),
					'price' 		=> $item->getPrice(),
					'ecomm_prodid' 	=> $this->getAdwordsEcommProdId($item)
				));
				
				/**
				 * Change values if configurable
				 */
				if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
				{
					if (!$this->useSimples())
					{
						$data->setId
						(
							$this->escape($item->getProduct()->getSku())
						);
						
						$data->setName
						(
							$this->escape($item->getProduct()->getName())
						);
						
						$data->setEcommProdid
						(
							$this->escape
							(
								$this->getAdwordsEcommProdId
								(
									$item->getProduct()
								)
							)
						);
					}
					else 
					{
						if($item->getHasChildren()) 
						{
							foreach($item->getChildrenItems() as $child) 
							{
								$data->setEcommProdid
								(
									$this->escape
									(
										$this->getAdwordsEcommProdId
										(
											$child
										)
									)
								);
							}
						}
					}
				}
				
				$google_tag_params->ecomm_prodid[] 		= 		  $data->getEcommProdid();
				$google_tag_params->ecomm_pvalue[] 		= (float) $data->getPrice();
				$google_tag_params->ecomm_pname[] 		= 		  $data->getName();
			}
			
			/**
			 * Set total value
			 */
			$google_tag_params->ecomm_totalvalue = (float) $this->getRevenueAdWords($order);
		}
		
		return $google_tag_params;
	}
	
	/**
	 * Get orders
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 */
	public function getOrders($block)
	{
		return $this->getOrdersCollection
		(
			(array) $block->getOrderIds()
		);
	}
	
	/**
	 * Get orders collection 
	 * 
	 * @param array $order_ids
	 * @return array
	 */
	public function getOrdersCollection(array $order_ids = [])
	{
		if (!$this->_orders)
		{
			if (!$order_ids)
			{
				$this->_orders = [];
			}
			else 			
			{
				$collection = $this->getSalesOrderCollection()->create();
				
				/**
				 * Filter applicable order ids
				 */
				$collection->addFieldToFilter('entity_id', ['in' => $order_ids]);
				
				foreach ($collection as $order)
				{
					if ($order->getPayment())
					{
						/**
						 * Get filter-out method 
						 * 
						 * @var [] $filter
						 */
						$filter = $this->getOrderFilterOutMethods();
						
						/**
						 * Get order payment method 
						 * 
						 * @var string $method
						 */
						$method = $order->getPayment()->getMethod();
						
						if (!in_array($method, $filter))
						{
							$this->_orders[] = $order;
						}
					}
					else 
					{
						$this->_orders[] = $order;
					}	
				}
			}
		}
		
		return $this->_orders;
	}
	
	/**
	 * Get Search push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass|boolean
	 */
	public function getSearchPush($block)
	{
		try 
		{
			if ('catalogsearch' === $this->request->getModuleName())
			{
				$list = $block->getLayout()->getBlock('search_result_list');
				
				if ($list)
				{
					$response = array
					(
						'ecommerce' 	=> array
						(
							'currencyCode' 	=> $this->getStore()->getCurrentCurrencyCode(),
							'actionField' => 
							[
								'list' => __('Search Results')
							],
							'impressions' => []
						)
					);
					
					$position = 1;
		
					$data = [];
					
					foreach ($this->getLoadedCollection($list) as $product)
					{
						/**
						 * Get all product categories
						 */
						$categories = $this->getCurrentStoreProductCategories($product);
						
						/**
						 * Cases when product does not exist in any category
						 */
						if (!$categories)
						{
							$categories[] = $this->getStoreRootDefaultCategoryId();
						}
						
						/**
						 * Load last category
						 */
						$category = $this->categoryRepository->get
						(
							end($categories)
						);
						
						$response['ecommerce']['impressions'][] = array
						(
							'list' 			=> __('Search Results')->__toString(),
							'category'		=> $this->getCategory($category),
							'id'			=> $product->getSku(),
							'name'			=> $product->getName(),
							'brand'			=> $this->getBrand
							(
								$product
							),
							'price'			=> $this->getPrice($product),
							'position'		=> $position++
						);
					}
					
					$response['currentStore'] = $this->getStoreName();
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
				$this->eventManager->dispatch('ec_get_search_attributes', ['transport' => $transport]);
				
				/**
				 * Get response
				 */
				$attributes = $transport->getAttributes();
				
				foreach ($response['ecommerce']['impressions'] as &$product)
				{
					foreach ($attributes as $key => $value)
					{
						$product[$key] = $value;
					}
				}
				
				unset($product);
	
				return (object) 
				[
					'push' 				=> $this->getJsonHelper()->encode($response),
					'google_tag_params' => array
					(
						'ecomm_pagetype' 	=> 'category',
						'ecomm_category'	=> __('Search Results')
					)
				];
			}
		}
		catch (\Exception $e){}
		
		return false;
	}

	/**
	 * Get visitor push
	 * 
	 * @param \Magento\Framework\View\Element\AbstractBlock $block
	 */
	public function getVisitorPush($block = null)
	{
		/**
		 * Get customer group
		 */
		
		$data = array
		(
			'visitorLoginState' 		=> $this->isLogged() ? __('Logged in') : __('Logged out'),
			'visitorLifetimeValue' 		=> 0,
			'visitorExistingCustomer' 	=> __('No')
		);
		
		if ($this->isLogged())
		{
			$data['visitorId'] = $this->getCustomer()->getId();
			
			/**
			 * Get customer order(s)
			 * 
			 * @var array
			 */
			$orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('customer_id', $this->getCustomer()->getId())->addFieldToFilter('status',['in' => $this->orderConfig->getVisibleOnFrontStatuses()])->setOrder('created_at','desc');
			
			$total = 0;
			
			foreach ($orders as $order)
			{
				$total += $order->getGrandTotal();
			}
	
			$data['visitorLifetimeValue'] = $total;
			
			if ($total > 0)
			{
				$data['visitorExistingCustomer'] = __('Yes');
				
				/**
				 * Returning customer 
				 * 
				 * @var \Anowave\Ec\Helper\Data $returnCustomer
				 */
				$this->returnCustomer = true;
			}
			
			$group = $this->groupRegistry->retrieve
			(
				$this->getCustomer()->getGroupId()
			);

			$data['visitorType'] = $group->getCustomerGroupCode();
		}
		else 
		{
			$group = $this->groupRegistry->retrieve(0);
			
			$data['visitorType'] = $group->getCustomerGroupCode();
		}
		
		$data['currentStore'] = $this->getStoreName();
		
		return $this->getJsonHelper()->encode($data);
	}
	
	/**
	 * Get Facebook Pixel Product View content 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @param \Magento\Catalog\Model\Category $category
	 * @return []
	 */
	public function getFacebookViewContentTrack(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Category $category)
	{
		return 
		[
			'content_type' 		=> 'product',
			'content_name' 		=> $product->getName(),
			'content_category' 	=> $this->getCategory($category),
			'content_ids' 		=> $product->getSku(),
			'currency' 			=> $this->getStore()->getCurrentCurrencyCode(),
			'value' 			=> $this->getPrice($product)
		];
	}
	
	public function getFacebookInitiateCheckoutTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	public function getFacebookAddToCartTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	public function getFacebookPurchaseTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	/**
	 * Use Facebook Pixel tracking
	 */
	public function facebook()
	{
		return 1 === (int) $this->getConfig('ec/facebook/active');
	}
	
	/**
	 * Get Facebook Pixel tracking code
	 */
	public function getFacebookPixelCode()
	{
		if ($this->facebook())
		{
			return (string) $this->getConfig('ec/facebook/facebook_pixel_code');
		}
		else 
		{
			return '';
		}
	}
	
	/**
	 * Check if customer is logged in
	 */
	public function isLogged()
	{
		if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH))
		{
			return true;
		}
		else if($this->session->isLoggedIn())
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get customer
	 */
	public function getCustomer()
	{
		if (!$this->customer)
		{
			if ($this->registry->registry('cache_session_customer_id') > 0)
			{
				$this->customer = $this->customerRepositoryInterface->getById($this->registry->registry('cache_session_customer_id'));
			}
		}
	
		return $this->customer;
	}
	
	/**
	 * Get Super Attributes
	 */
	public function getSuper()
	{
		$super = [];
		
		if ($this->registry->registry('current_product'))
		{
			$product = $this->registry->registry('current_product');
			
			if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $product->getTypeId())
			{
				$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
								 	
			 	foreach($attributes as $attribute)
			 	{
			 		$object = $attribute->getProductAttribute();
			 		
			 		$super[] = array
			 		(
			 			'id' 				=> $object->getAttributeId(),
			 			'label' 			=> $this->getAttributeLabel($object),
			 			'code'				=> $object->getAttributeCode(),
			 			'options'			=> $this->getAttributeOptions($attribute)
			 		);
			 	}
			}
		}

		return $this->getJsonHelper()->encode($super);
	}
	
	/**
	 * Get bundle items
	 */
	public function getBundle()
	{
		$bundles = [];
		$options = [];
		
		if (null !== $product = $this->registry->registry('current_product'))
		{
			if (\Magento\Bundle\Model\Product\Type::TYPE_CODE === $product->getTypeId())
			{
				foreach ($product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product),$product) as $bundle) 
				{
					$bundles[$bundle->getOptionId()][$bundle->getId()] = 
					[
						'id' 		=> $bundle->getSku(),
						'name' 		=> $bundle->getName(),
						'price'		=> $bundle->getPrice(),
						'quantity' 	=> $bundle->getSelectionQty(),
					];
				}

				foreach ($product->getTypeInstance(true)->getOptionsCollection($product) as $option) 
				{
					$options[$option->getOptionId()] = 
					[
						'option_title' => $option->getDefaultTitle(),
						'option_type'  => $option->getType()
						
					];
				}
			}
		}
		
		return $this->jsonHelper->encode(
		[
			'bundles' => $bundles,
			'options' => $options
		]);
	}
	
	/**
	 * Get attribute label 
	 * 
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
	 */
	protected function getAttributeLabel(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
	{
		return ($this->useDefaultValues() ? $attribute->getFrontendLabel() : $attribute->getStoreLabel());
	}
	
	/**
	 * Get attribute options
	 * 
	 * @param Object $attribute
	 */
	protected function getAttributeOptions($attribute)
	{
		$options = [];
		
		foreach ($attribute->getOptions() as $option)
		{
			$options[] = $option;
		}
			
		if ($this->useDefaultValues())
		{
			try 
			{
				foreach ($options as &$option)
				{
					$this->optionCollection->clear();
					$this->optionCollection->getSelect()->reset(\Zend_Db_Select::WHERE);
					$this->optionCollection->getSelect()->where('main_table.option_id IN (?)',[$option['value_index']]);
					$this->optionCollection->getSelect()->group('main_table.option_id');
					
					/**
					 * Set admin label
					 *
					 * @var string
					*/
					$option['admin_label'] = $this->optionCollection->getFirstitem()->getValue();
				}
				
				unset($option);
			}
			catch (\Exception $e)
			{
				return [];
			}
		}
			
		return $options;
	}
	
	/**
	 * Get final price of product 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getPrice(\Magento\Catalog\Model\Product $product)
	{
		/**
		 * Get final price 
		 * 
		 * @var float
		 */
		if (\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE == $product->getTypeId())
		{
			$products = $product->getTypeInstance()->getAssociatedProducts($product);
			
			if ($products)
			{
				$price = (float) $product->getPriceInfo()->getPrice('final_price')->getValue();
			}
			else 
			{
				$price = 0;	
			}
		}
		else 
		{
			$price = (float) $product->getPriceInfo()->getPrice('final_price')->getValue();
		}

		/**
		 * Allow others to modify price
		 */
		$this->eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => 1]);
		
		$finalPrice = (float) $product->getData('final_price');
		
		if ($finalPrice && $finalPrice < $price)
		{
			$price = $finalPrice;
		}
		
		return (float) $this->catalogData->getTaxPrice($product, $price, true,null,null,null, null,null,false);
	}
	
	/**
	 * Get category 
	 * 
	 * @param \Magento\Catalog\Model\Category $category
	 */
	public function getCategory(\Magento\Catalog\Model\Category $category)
	{
		if (0 !== (int) $this->getConfig('ec/options/use_segments'))
		{
			return $this->getCategorySegments($category);
		}
		
		return $category->getName();
	}
	
	/**
	 * Get detail list (correlates with category)
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @param \Magento\Catalog\Model\Category $category
	 * 
	 * @return string
	 */
	public function getCategoryDetailList(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Category $category)
	{
		return $category->getName();
	}
	
	/**
	 * Get category list name
	 * 
	 * @param \Magento\Catalog\Model\Category $category
	 */
	public function getCategoryList(\Magento\Catalog\Model\Category $category)
	{
		return $category->getName();
	}
	
	/**
	 * Retrieve category and it's parents separated by chr(47)
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function getCategorySegments(\Magento\Catalog\Model\Category $category)
	{
		$segments = [];
	
		foreach ($category->getParentCategories() as $parent)
		{
			$segments[] = $parent->getName();
		}
	
		if (!$segments)
		{
			$segments[] = $category->getName();
		}
	
		return trim(join(chr(47), $segments));
	}
	
	/**
	 * Get product brand 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getBrand(\Magento\Catalog\Model\Product $product)
	{
		if (array_key_exists((int) $product->getId(), $this->_brandMap))
		{
			return $this->_brandMap[$product->getId()];
		}

		switch ($product->getTypeId())
		{
			case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
			case \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL: 
			case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
				
				$attributes = array_filter([$this->getConfig('ec/options/use_brand_attribute')]);
				
				if (!$attributes)
				{
					$attributes = ['manufacturer'];
				}
				
				foreach ($attributes as $code)
				{
					$attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code);
					
					if ($attribute->getId() && $attribute->usesSource())
					{
						$brand = (string) $product->getAttributeText($code);

						if (!$brand)
						{
							/**
							 * Get value 
							 * 
							 * @var string $value
							 */
							$value = (int) $product->getResource()->getAttributeRawValue($product->getId(), $code, $this->getStore()->getId());
							
							if ($value > 0)
							{
								/**
								 * Get text representation
								 */
								$brand = $attribute->getSource()->getOptionText($value);
							}
						}
						
						$this->_brandMap[(int) $product->getId()] = $brand;
						
						return $brand;
					}
					else 
					{
						/**
						 * Static brands
						 */
						
						$brand = $product->getResource()->getAttributeRawValue($product->getId(), $code, $this->getStore()->getId());
						
						if ($brand)
						{
							$this->_brandMap[(int) $product->getId()] = $brand;
							
							return $brand;
						}
					}
				}
				break;
			case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
				/**
				 * @todo Implement brand tracking for bundle products
				 */
				break;
		}
		
		/**
		 * Return empty brand
		 */
		return '';
	}
	
	/**
	 * Get Facebook value key
	 */
	public function getFacebookValueKey()
	{
		$key = $this->getConfig('ec/facebook/facebook_value');
		
		if (!in_array($key, array('revenue','subtotal')))
		{
			$key = \Anowave\Ec\Model\System\Config\Source\Value::KEY_REVENUE;
		}
		
		return $key;
	}
	
	/**
	 * Get current store
	 */
	public function getStore()
	{
		return $this->storeManager->getStore();
	}
	
	/**
	 * Set store name
	 */
	public function getStoreName()
	{
		return $this->getStore()->getName();
	}
	
	public function getCurrency()
	{
		return $this->getStore()->getCurrentCurrencyCode();
	}

	/**
	 * Get body snippet
	 * 
	 * @return String
	 */
	public function getBodySnippet()
	{
		return $this->getConfig('ec/general/code_body');
	}
	
	/**
	 * Get head snippet
	 * 
	 * @return String
	 */
	public function getHeadSnippet()
	{
		return $this->getConfig('ec/general/code_head');
	}
	
	/**
	 * Get Google Optimize Page Hiding Snippet
	 * 
	 * @return mixed
	 */
	public function getGoogleOptimizePageHidingSnippet()
	{
		return $this->getConfig('ec/optimize/use_optimize_page_hiding_snippet');
	}
	
	/**
	 * Check for standalone optimize implementation
	 * 
	 * @return boolean
	 */
	public function getGoogleOptimizeIsStandalone()
	{
		if (!$this->isBetaMode())
		{
			return false;
		}
		
		/**
		 * Check Optimize Container Id
		 */
		if ('' === $this->getGoogleOptimizeContainerId())
		{
			return false;
		}
		
		/**
		 * Check Google Analytics Id
		 */
		if ('' === $this->getGoogleOptimizeAnalyticsId())
		{
			return false;
		}
		
		return \Anowave\Ec\Model\System\Config\Source\Optimize\Implementation::I_STANDALONE === (int) $this->getConfig('ec/optimize/implementation');
	}
	
	/**
	 * Check for assisted optimize implementation
	 * 
	 * @return boolean
	 */
	public function getGoogleOptimizeIsAssisted()
	{
		return \Anowave\Ec\Model\System\Config\Source\Optimize\Implementation::I_ASSISTED === (int) $this->getConfig('ec/optimize/implementation');
	}
	
	/**
	 * Get Google Optimize Container ID
	 *
	 * @return mixed
	 */
	public function getGoogleOptimizeContainerId()
	{
		return (string) trim($this->getConfig('ec/optimize/use_optimize_container_id'));
	}
	
	/**
	 * Google Google Optimize Universal Analytics ID
	 * 
	 * @return string
	 */
	public function getGoogleOptimizeAnalyticsId()
	{
		return (string) trim($this->getConfig('ec/general/account'));
	}
	
	/**
	 * Check if contact form has been submitted
	 * 
	 * @return JSON|boolean
	 */
	public function getContactEvent()
	{
		$event = $this->session->getContactEvent();
		
		if ($event)
		{
			$this->session->unsetData('contact_event');
			
			return $event;
		}
		
		return false;
	}
	
	public function getCartUpdateEvent()
	{
		$event = $this->session->getCartUpdateEvent();
		
		if ($event)
		{
			$this->session->unsetData('cart_update_event');
			
			return $event;
		}
		
		return false;
	}
	
	/**
	 * Check if contact form has been submitted
	 *
	 * @return JSON|boolean
	 */
	public function getNewsletterEvent()
	{
		$event = $this->session->getNewsletterEvent();
		
		if ($event)
		{
			$this->session->unsetData('newsletter_event');
			
			return $event;
		}
		
		return false;
	}
	
	
	public function getStoreRootDefaultCategoryId()
	{
		$roots = $this->getAllStoreRootCategories();
		
		if ($roots)
		{
			return (int) reset($roots);
			
		}
		return null;
	}
	
	
	/**
	 * Get root category id
	 *
	 * @param unknown $store
	 * @throws \Exception
	 */
	public function getStoreRootCategoryId($store)
	{
		if (is_int($store))
		{
			$store = $this->storeManager->getStore($store);
		}
		
		if (is_string($store))
		{
			foreach ($this->storeManager->getStores() as $model)
			{
				if ($model->getCode() == $store)
				{
					$store = $model;
					
					break;
				}
			}
		}
		
		if ($store instanceof \Magento\Store\Model\Store)
		{
			return $store->getRootCategoryId();
		}
		
		throw new \Exception("Store $store does not exist anymore");
	}
	
	/**
	 * Get an associative array of [store_id => root_category_id] values for all stores
	 * 
	 * @return array
	 */
	public function getAllStoreRootCategories()
	{
		$roots = [];
		
		foreach ($this->storeManager->getStores() as $store)
		{
			if ($store->getId() == $this->getStore()->getId())
			{
				$roots[$store->getId()] = $store->getRootCategoryId();
			}
		}
		
		return $roots;
	}

	/**
	 * Check if module is active
	 * 
	 * @return boolean
	 */
	public function isActive()
	{
		return 0 !== (int) $this->getConfig('ec/general/active');
	}
	
	/**
	 * Check if AdWords Conversion Tracking is active and can be triggered (with consent check)
	 * 
	 * @param bool $consent
	 * @return boolean
	 */
	public function isAdwordsConversionTrackingActive(bool $consent = false)
	{
		$active = 1 === (int) $this->getConfig('ec/adwords/conversion');
		
		/**
		 * Check for consent
		 */
		if ($consent && $this->supportCookieDirective())
		{
			if ($this->isCookieConsentAccepted())
			{
				return $active;
			}

			return false;
		}

		return $active;
	}
	
	/**
	 * Check if module is in beta mode 
	 * 
	 * @return boolean
	 */
	public function isBetaMode()
	{
		return 1 === (int) $this->getConfig('ec/beta/mode');
	}
	
	/**
	 * Check if using GTAG implementation 
	 * 
	 * @return boolean
	 */
	public function useAdwordsConversionTrackingGtag()
	{
		return 1 === (int) $this->getConfig('ec/adwords/gtag');
	}
	
	/**
	 * Remove confirmation
	 * 
	 * @return string
	 */
	public function useRemoveConfirm()
	{
		return $this->getJsonHelper()->encode($this->getUseRemoveConfirm());
	}
	
	/**
	 * Get localStorage flag
	 * 
	 * @return string
	 */
	public function useLocalStorage()
	{
		return $this->getJsonHelper()->encode($this->getUseLocalStorage());
	}
	
	/**
	 * Local storage 
	 * 
	 * @return boolean
	 */
	public function getUseLocalStorage()
	{
		return 1 === (int) $this->getConfig('ec/options/use_local_storage');
	}
		
	/**
	 * Remove confirmation
	 *
	 * @return string
	 */
	public function getUseRemoveConfirm()
	{
		return 1 === (int) $this->getConfig('ec/options/use_remove_confirm');
	}
	
	/**
	 * Get Gtag site tag
	 *
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagSiteTag()
	{
		return (string) $this->getConfig('ec/adwords/gtag_global_site_tag');
	}
	
	/**
	 * Gtag "send to" parameter
	 * 
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagSendToParameter()
	{
		return (string) $this->getConfig('ec/adwords/gtag_send_to');
	}
	
	
	/**
	 * Get AdWords Conversion Tracking conversion event JSON 
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagConvesionEvent(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		return $this->getJsonHelper()->encode(
		[
			'send_to' 			=> $this->getAdwordsConversionTrackingGtagSendToParameter(),
			'value' 			=> $this->getRevenue($order),
			'currency' 			=> $this->getStore()->getCurrentCurrencyCode(),
			'transaction_id' 	=> $order->getIncrementId()
		], JSON_UNESCAPED_SLASHES);
	}
	
	/**
	 * Use default admin labels for product variants
	 * 
	 * @return boolean	
	 */
	public function useDefaultValues()
	{
		return 1 === (int) $this->getConfig('ec/options/use_skip_translate');
	}
	
	/**
	 * Use simple SKU(s) instead of configurable parent SKU. Applicable for configurable products only.
	 * 
	 * @return boolean
	 */
	public function useSimples()
	{
		return 1 === (int) $this->getConfig('ec/options/use_simples');
	}
	
	/**
	 * Check for AMP support
	 * 
	 * @return boolean
	 */
	public function supportAmp()
	{
		return 1 === (int) $this->getConfig('ec/amp/enable');
	}
	
	/**
	 * Support extended AdWords Dynamic Remarketing (for other sites)
	 * 
	 * @return boolean
	 */
	public function supportDynx()
	{
		return 1 === (int) $this->getConfig('ec/adwords/dynx');
	}
	
	/**
	 * Add support for internal search
	 * 
	 * @return boolean
	 */
	public function supportInternalSearch()
	{
		return 1 === (int) $this->getConfig('ec/search/enable');
	}
	
	/**
	 * Check if cookie directive support is enabled 
	 * 
	 * @return boolean
	 */
	public function supportCookieDirective()
	{
		return 1 === (int) $this->getConfig('ec/cookie/enable');
	}
	
	/**
	 * Check if cookie consent is accepted 
	 * 
	 * @return boolean
	 */
	public function isCookieConsentAccepted()
	{
		if (1 === (int) $this->directive->get())
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get cookie directive content 
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveContent()
	{
		return $this->getConfig('ec/cookie/content');
	}
	
	/**
	 * Get cookie consent mode
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveConsentMode()
	{
		return $this->getConfig('ec/cookie/mode');
	}
	
	/**
	 * Check if consent mode is segment 
	 * 
	 * @return boolean
	 */
	public function getCookieDirectiveIsSegmentMode()
	{
		return \Anowave\Ec\Model\System\Config\Source\Consent\Mode::SEGMENT === (int) $this->getCookieDirectiveConsentMode();	
	}
	
	/**
	 * Get cookie directive conent segments
	 * 
	 * @return string[]
	 */
	public function getCookieDirectiveConsentSegments()
	{
		if ($this->getCookieDirectiveIsSegmentMode())
		{
			return 
			[
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT,
			];
		}
		
		return 
		[
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT
		];
	}
	
	/**
	 * Get cookie directive background color
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveBackgroundColor()
	{
		return $this->getConfig('ec/cookie/content_background_color');
	}
	
	/**
	 * Get filter-out payment methods
	 * 
	 * @return []
	 */
	public function getOrderFilterOutMethods()
	{
		$methods = (string) $this->getConfig('ec/options/use_disable_payment_method_tracking');
		
		return array_filter(explode(chr(44), $methods));
	}
	
	/**
	 * Get cookie directive text color
	 *
	 * @return mixed
	 */
	public function getCookieDirectiveTextColor()
	{
		return $this->getConfig('ec/cookie/content_text_color');
	}
	
	/**
	 * Get cookie directive accept link color
	 *
	 * @return mixed
	 */
	public function getCookieDirectiveTextAcceptColor()
	{
		return $this->getConfig('ec/cookie/content_accept_color');
	}
	
	/**
	 * Get internal search dimensions
	 * 
	 * @return number
	 */
	public function getInternalSearchDimension()
	{
		$dimension = (int) $this->getConfig('ec/search/dimension');
		
		if (!$dimension)
		{
			$dimension = \Anowave\Ec\Helper\Constants::INTERNAL_SEARCH_DEFAULT_DIMENSION;
		}
		
		return $dimension;
	}

	/**
	 * Add internal search dimension 
	 * 
	 * @param array $attributes
	 */
	public function addInternalSearchDimension(array &$attributes = [])
	{
		if ($this->supportInternalSearch())
		{
			if (null !== $query = $this->request->getParam('q'))
			{
				/**
				 * Basic sanitization
				 */
				$query = preg_replace('/[^a-zA-Z0-9]/i','', strip_tags($query));
				
				/**
				 * Add query to parameters
				 */
				$attributes["dimension{$this->getInternalSearchDimension()}"] = $query;
			}
		}
		
		return $attributes;
	}
	
	
	/**
	 * Check if current Magento is Enterprise (EE) edition
	 * 
	 * @return boolean
	 */
	public function isEnterprise()
	{
		return $this->productMetadata->getEdition() === 'Enterprise';
	}
	
	/**
	 * Check if current Magento is Community (CE) edition
	 *
	 * @return boolean
	 */
	public function isCommunity()
	{
		return $this->productMetadata->getEdition() === 'Community';
	}
	
	/**
	 * Get product categories 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getCurrentStoreProductCategories(\Magento\Catalog\Model\Product $product)
	{
		return array_intersect((array) $product->getCategoryIds(), $this->getCurrentStoreCategories());
	}
	
	/**
	 * Get current store categories 
	 * 
	 * @return NULL[]
	 */
	public function getCurrentStoreCategories()
	{
		if (!$this->currentCategories)
		{
			foreach ($this->categoryHelper->getStoreCategories() as $item)
			{
				$this->currentCategories[] = (string) $item->getId();
			}
		}
		
		return $this->currentCategories;
	}
	
	/**
	 * Check if customer is returning customer
	 * 
	 * @return boolean
	 */
	public function getIsReturnCustomer()
	{
		return $this->getJsonHelper()->encode($this->returnCustomer);
	}
	
	/**
	 * Get ecomm_prodid
	 * 
	 * @param \Magento\Framework\Api\ExtensibleDataInterface $item
	 * @return string
	 */
	public function getAdwordsEcommProdId(\Magento\Framework\Api\ExtensibleDataInterface $item)
	{
		/**
		 * Get attribute 
		 * 
		 * @var string $attribute
		 */
		$attribute = strtolower
		(
			$this->getConfig('ec/adwords/ecomm_prodid')
		);
		
		/**
		 * Fallback to default attribute 
		 * 
		 */
		if (!in_array($attribute, [\Anowave\Ec\Model\System\Config\Source\Id::ID_ID, \Anowave\Ec\Model\System\Config\Source\Id::ID_SKU]))
		{
			$attribute = \Anowave\Ec\Model\System\Config\Source\Id::ID_SKU;
		}
		
		switch ($attribute)
		{
			case \Anowave\Ec\Model\System\Config\Source\Id::ID_ID: 	
			{
				/**
				 * Checkout/cart items
				 */
				if ($item instanceof \Magento\Quote\Model\Quote\Item)
				{
					return (int) $item->getProductId();	
				}

				/**
				 * Purchase items
				 */
				if ($item instanceof \Magento\Sales\Model\Order\Item)
				{
					return (int) $item->getProductId();
				}
				
				return (int) $item->getId();
			}
			case \Anowave\Ec\Model\System\Config\Source\Id::ID_SKU: 
			{
				return (string) $item->getSku();
			}
		}
		
		return (string) $item->getSku();
	}
	
	/**
	 * Get module version
	 * 
	 * @return float
	 */
	public function getVersion()
	{
		return $this->moduleList->getOne('Anowave_Ec')['setup_version'];
	}
	
	/**
	 * Category items selector 
	 * 
	 * @return XPath (string)
	 */
	public function getListSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_SELECTOR;
	}
	
	/**
	 * Add to wishlist selector
	 *
	 * @return XPath (string)
	 */
	public function getWishlistSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/add_wishlist'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_ADD_WISHLIST_SELECTOR;
	}
	
	/**
	 * Add to wishlist selector
	 *
	 * @return XPath (string)
	 */
	public function getCompareSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/add_compare'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_ADD_COMPARE_SELECTOR;
	}
	
	/**
	 * NewProduct widget selector
	 *
	 * @return XPath (string)
	 */
	public function getListWidgetSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_SELECTOR;
	}
	
	/**
	 * NewProduct widget click selector
	 *
	 * @return XPath (string)
	 */
	public function getListWidgetClickSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget_click'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_CLICK_SELECTOR;
	}
	
	/**
	 * Get widget add to cart selector
	 * 
	 * @return string
	 */
	public function getListWidgetCartCategorySelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget_cart'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_CART_SELECTOR;
	}
	
	
	/**
	 * Category items click selector
	 *
	 * @return XPath (string)
	 */
	public function getListClickSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/click'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_CLICK_SELECTOR;
	}
	
	/**
	 * Add to cart selector (product detail page)
	 *
	 * @return XPath (string)
	 */
	public function getCartSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_SELECTOR;
	}
	
	/**
	 * Add to cart selector (direct button from categories)
	 *
	 * @return XPath (string)
	 */
	public function getCartCategorySelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart_list'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_CATEGORY_SELECTOR;
	}
	
	/**
	 * Remove from cart selector
	 *
	 * @return XPath (string)
	 */
	public function getDeleteSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart_delete'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_DELETE_SELECTOR;
	}
	
	/**
	 * Get onclick binding type
	 *
	 * @return boolean
	 */
	public function useClickHandler()
	{
		return 0 === (int) $this->getConfig('ec/selectors/bind');
	}
	
	/**
	 * Get jQuery binding type
	 *
	 * @return boolean
	 */
	public function useOnHandler()
	{
		return !$this->useClickHandler();
	}
	
	/**
	 * Check whether event callback should be used
	 *
	 * @return number
	 */
	public function getEventCallback()
	{
		if ($this->useClickHandler())
		{
			return $this->getJsonHelper()->encode(true);
		}
		
		return $this->getJsonHelper()->encode(false);
	}
	
	/**
	 * Get search attributes 
	 * 
	 * @return string
	 */
	public function getSearchAttributes()
	{
		return $this->attributes->getAttributes();
	}
	/**
	 * Get event manager 
	 * 
	 * @return \Magento\Framework\Event\ManagerInterface
	 */
	public function getEventManager()
	{
		return $this->eventManager;
	}
	
	/**
	 * Get sales order collection
	 *
	 * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	public function getSalesOrderCollection()
	{
		return $this->_salesOrderCollection;
	}
	
	/**
	 * Get JSON helper 
	 * 
	 * @return \Anowave\Ec\Helper\Json
	 */
	public function getJsonHelper()
	{
		return $this->jsonHelper;
	}
	
	/**
	 * Escape quotes
	 * 
	 * @param string $string
	 * @return string
	 */
	public function escape($data)
	{
		return addcslashes($data, '\'');
	}
	
	/**
	 * Escape string for HTML5 data attribute 
	 * 
	 * @param string $data
	 * @return string
	 */
	public function escapeDataArgument($data)
	{
		return str_replace(array('"','\''), array('&quot;','&apos;'), $data);
	}
}