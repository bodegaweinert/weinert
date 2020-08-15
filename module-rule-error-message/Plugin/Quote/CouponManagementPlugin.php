<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 26/09/18
 * Time: 01:59
 */

namespace Combinatoria\RuleErrorMessage\Plugin\Quote;

use Magento\Quote\Model\CouponManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagementPlugin
{

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * Rule factory
     *
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CouponFactory $couponFactory,
        RuleFactory $ruleFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->couponFactory = $couponFactory;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function aroundSet(
        CouponManagement $subject,
        callable $proceed,
        $cartId,
        $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setCouponCode($couponCode);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply coupon code'));
        }
        if ($quote->getCouponCode() != $couponCode) {

            $coupon = $this->couponFactory->create();
            $coupon->load($couponCode, 'code');

            if ($coupon->getId()){
                $rule = $this->ruleFactory->create();
                $rule->load($coupon->getRuleId());

                if ($rule->getRuleId()){
                    $errorMessage = $rule->getErrorMessage();
                    if ($errorMessage != null && $errorMessage != ''){
                        throw new NoSuchEntityException(__($errorMessage));
                    }
                }
            }
            
            
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }
        return true;
    }


}