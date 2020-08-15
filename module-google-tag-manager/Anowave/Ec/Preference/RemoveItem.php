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

namespace Anowave\Ec\Preference;

class RemoveItem extends \Magento\Checkout\Controller\Sidebar\RemoveItem
{
	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $dataHelper = null;
	
	/**
	 * @var \Magento\Catalog\Model\ProductRepository
	 */
	protected $productRepository;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Checkout\Model\Sidebar $sidebar
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Anowave\Ec\Helper\Data $dataHelper
	 * @param \Magento\Catalog\Model\ProductRepository $productRepository
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Sidebar $sidebar,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Checkout\Model\Cart $cart,
		\Anowave\Ec\Helper\Data $dataHelper,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository
	) 
	{
		parent::__construct($context, $sidebar, $logger, $jsonHelper, $resultPageFactory);
		
		$this->cart = $cart;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $dataHelper
		 */
		$this->dataHelper = $dataHelper;
		
		/**
		 * Set product repository 
		 * 
		 * @var \Magento\Catalog\Model\ProductRepository $productRepository
		 */
		$this->productRepository = $productRepository;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
	}
	
	/**
     * Compile JSON response
     *
     * @param string $error
     * @return \Magento\Framework\App\Response\Http
     */
    protected function jsonResponse($error = '')
    {
        $response = $this->sidebar->getResponseData($error);
        
        $item = $this->cart->getQuote()->getItemById((int) $this->getRequest()->getParam('item_id'));
        
        if ($item instanceof \Magento\Quote\Api\Data\CartItemInterface) 
        {
        	/**
        	 * Load product 
        	 * 
        	 * @var \Magento\Catalog\Api\Data\ProductInterface $product
        	 */
        	$product = $this->productRepository->getById
        	(
        		$item->getProductId()
        	);

        	$data = 
        	[
        		'event' 	=> 'removeFromCart',
        		'ecommerce' =>
        		[
        			'remove' =>
        			[
        				'products' =>
        				[
        					[
        						'id'  		=> ($this->dataHelper->useSimples() ? $item->getSku() : $product->getSku()),
        						'name' 		=> $item->getName(),
        						'quantity' 	=> $item->getQty(),
        						'price'		=> $item->getPrice(),
        						'brand'		=> $this->dataHelper->getBrand($product)
        					]
        				]
        			]
        		]
        	];
        	
        	/**
        	 * Get all product categories
        	 */
        	$categories = $this->dataHelper->getCurrentStoreProductCategories($product);
        		
        	if ($categories)
        	{
        		/**
        		 * Load last category
        		 */
        		$category = $this->categoryRepository->get
        		(
        			end($categories)
        		);
        		
        		/**
        		 * Set category name
        		 */
        		$data['ecommerce']['remove']['products'][0]['category'] = $this->dataHelper->getCategory($category);
        		
        		/**
        		 * Set action field
        		 */
        		$data['ecommerce']['remove']['actionField'] = 
        		[
        			'list' => $this->dataHelper->getCategoryList($category)
        		];
        	}
        	
        	/**
        	 * Set response push
        	 */
        	$response['dataLayer'] = $data;
        }
        
        return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($response));
    }
}