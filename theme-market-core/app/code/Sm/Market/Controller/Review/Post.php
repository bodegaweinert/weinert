<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sm\Market\Controller\Review;

use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\Review;

class Post extends ProductController
{
    /**
     * Submit new review action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $result = [];

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }
        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var \Magento\Review\Model\Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $review->setTitle('-');
            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $result['success'] = true;
                    $result['messages'] =  __('You submitted your review for moderation.');
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $result['success'] = false;
                    $result['messages'] =  __('We can\'t post your review right now.');
                }
            } else {
                $result['success'] = false;


                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $result['messages'] .= '<br/>'. $errorMessage;

                    }
                } else {
                    $result['messages'] =  __('We can\'t post your review right now.');
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
}
