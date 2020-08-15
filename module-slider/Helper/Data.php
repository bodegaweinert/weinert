<?php
namespace Combinatoria\Slider\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    private $_scopeConfig;

    public function __construct(
        Context $context
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
}
