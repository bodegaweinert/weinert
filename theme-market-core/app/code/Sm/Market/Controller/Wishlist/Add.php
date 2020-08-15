<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sm\Market\Controller\Wishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Adding new item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $result = [];
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/');
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            $result['success'] = false;
            $result['messages'] =  __('Page not found.');
        } else {
            $session = $this->_customerSession;

            $requestParams = $this->getRequest()->getParams();

            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }

            $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
            if (!$productId) {
                $resultRedirect->setPath('*/');
                return $resultRedirect;
            }

            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product || !$product->isVisibleInCatalog()) {
                $result['success'] = false;
                $result['messages'] =  __('We can\'t specify a product.');
            } else {
                try {
                    $buyRequest = new \Magento\Framework\DataObject($requestParams);

                    $resultW = $wishlist->addNewItem($product, $buyRequest);
                    if (is_string($resultW)) {
                        $result['success'] = false;
                        $result['messages'] =  __('We can\'t add the item to Wish List right now.');
                        //throw new \Magento\Framework\Exception\LocalizedException(__($result));
                    }
                    $wishlist->save();

                    $this->_eventManager->dispatch(
                        'wishlist_add_product',
                        ['wishlist' => $wishlist, 'product' => $product, 'item' => $resultW]
                    );

                    $referer = $session->getBeforeWishlistUrl();
                    if ($referer) {
                        $session->setBeforeWishlistUrl(null);
                    } else {
                        $referer = $this->_redirect->getRefererUrl();
                    }

                    $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

                    $result['success'] = true;
                    $result['messages'] =  __('Listo, agregaste %1 a tu lista de favoritos.',$product->getName());

                    $result['wishlistCount'] = $this->createCounter($this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->getItemCount());

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $result['success'] = false;
                    $result['messages'] =  __('We can\'t add the item to Wish List right now: %1.', $e->getMessage());

                } catch (\Exception $e) {
                    $result['success'] = false;
                    $result['messages'] =  __('We can\'t add the item to Wish List right now.');

                }
            }
        }

        return $this->_jsonResponse($result);
    }

    protected function _jsonResponse($result)
    {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }

    protected function createCounter($count)
    {
        if ($count > 1) {
            return __('%1 items', $count);
        } elseif ($count == 1) {
            return __('1 item');
        }
        return null;
    }
}
