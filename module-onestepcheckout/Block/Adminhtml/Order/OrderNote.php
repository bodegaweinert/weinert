<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 16/05/18
 * Time: 22:01
 */

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;

class OrderNote extends AbstractOrder
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        array $data = []
    ) {

        parent::__construct($context, $registry, $adminHelper, $data);
    }

}