<?php

namespace Combinatoria\OrderWorkflow\Block\Adminhtml\Order\View\Tab;

class Info {

    public function afterGetPaymentHtml(\Magento\Sales\Block\Adminhtml\Order\View\Tab\Info $subject, $result){

        $paymentHtml = $result;
        $paymentHtml = str_replace('&lt;','<',$paymentHtml);
        $paymentHtml = str_replace('&gt;','>',$paymentHtml);
        $paymentHtml = str_replace('&amp;','&',$paymentHtml);
        $paymentHtml = str_replace('&quot;','"',$paymentHtml);

        return $paymentHtml;

    }
}