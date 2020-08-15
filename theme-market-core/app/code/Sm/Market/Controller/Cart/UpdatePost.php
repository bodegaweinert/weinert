<?php

namespace Sm\Market\Controller\Cart;

class UpdatePost extends \Magento\Checkout\Controller\Cart
{
    public function execute(){
        try {

            $result = [];
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                    $this->cart->getQuote()->setCustomerId(null);
                }

                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->cart->updateItems($cartData)->save();

                $result['success'] = true;
                $result['messages'] =  __('Cart was updated successfully.');

                $_layout  = $this->_objectManager->get('Magento\Framework\View\LayoutInterface');
                $_layout->getUpdate()->load([ 'market_checkout_cart_index', 'checkout_cart_item_renderers','checkout_item_price_renderers']);
                $_layout->generateXml();
                $_output = $_layout->getOutput();
                $result['content'] = $_output;

            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $errorMessage =  $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage());
            //$this->messageManager->addError($errorMessage);

            $result['success'] = false;
            $result['messages'] =  $errorMessage;

        } catch (\Exception $e) {
            //$this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);

            $result['success'] = false;
            $result['messages'] =   __('We can\'t update the shopping cart.');
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