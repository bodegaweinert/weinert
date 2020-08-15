<?php

namespace Sm\Market\Controller\Cart;

class RefreshCart extends \Magento\Checkout\Controller\Cart
{
    public function execute(){

        $result['success'] = true;

        $_layout  = $this->_objectManager->get('Magento\Framework\View\LayoutInterface');
        $_layout->getUpdate()->load([ 'market_checkout_cart_index', 'checkout_cart_item_renderers','checkout_item_price_renderers']);
        $_layout->generateXml();
        $_output = $_layout->getOutput();
        $result['content'] = $_output;

        return $this->_jsonResponse($result);
    }

    protected function _jsonResponse($result)
    {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}