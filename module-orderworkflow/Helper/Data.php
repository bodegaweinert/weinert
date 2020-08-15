<?php
namespace Combinatoria\OrderWorkflow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;

Class Data extends AbstractHelper {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->_scopeConfig = $context;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function getStatusOrder(){
        $statusOrder = array();

        $newState = array('pending');
        $pendingPaymentState = array('pending_payment');
        $paymentAccreditedState = array('payment_accredited');
        $invoicedState = array('invoice_pending', 'invoiced');
        $shippingState = array('shipping_pending', 'delivered', 'received');
        $completeState = array('complete');

        $newStateAvailable = $this->getConfigValue('cmb_ow/states_customization/state_new',0);
        $pendingPaymentStateAvailable = $this->getConfigValue('cmb_ow/states_customization/state_pending_payment',0);
        $paymentAccreditedAvailable = $this->getConfigValue('cmb_ow/states_customization/state_payment_accredited',0);
        $invoicedStateAvailable = $this->getConfigValue('cmb_ow/states_customization/state_invoiced',0);
        $shippingStateAvailable = $this->getConfigValue('cmb_ow/states_customization/state_shipping',0);
        $completeStateAvailable = $this->getConfigValue('cmb_ow/states_customization/state_complete',0);

        if ($newStateAvailable){
            foreach ($newState as $status){
                array_push($statusOrder,$status);
            }
        }

        if ($pendingPaymentStateAvailable){
            foreach ($pendingPaymentState as $status){
                array_push($statusOrder,$status);
            }
        }

        if ($paymentAccreditedAvailable){
            foreach ($paymentAccreditedState as $status){
                array_push($statusOrder,$status);
            }
        }

        if ($invoicedStateAvailable){
            foreach ($invoicedState as $status){
                array_push($statusOrder,$status);
            }
        }

        if ($shippingStateAvailable){
            foreach ($shippingState as $status){
                array_push($statusOrder,$status);
            }
        }

        if ($completeStateAvailable){
            foreach ($completeState as $status){
                array_push($statusOrder,$status);
            }
        }

        return $statusOrder;

    }

    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getStateByStatus( $status){
        $return = '';
        switch ($status){
            case 'pending': $return = 'new'; break;
            case 'pending_payment': $return = 'pending_payment'; break;
            case 'payment_accredited': $return = 'payment_accredited'; break;
            case 'invoice_pending':
            case 'invoiced': $return = 'invoiced'; break;
            case 'shipping_pending':
            case 'delivered':
            case 'received': $return = 'shipping'; break;
        }

        return $return;
    }

    public function getStatusesForButton($status){
        $statuses = $this->getStatusOrder();
        $founded = false;
        $result = array('next' => '', 'options' => []);
        $options = [];
        $next = '';



        for ($i = 0; $i < count($statuses); $i++){
            if ($next != ''){
                $options[] = array('label' => $this->getStatusLabel($statuses[$i]), 'code'=> $statuses[$i]);
            }
            if ($founded){
                $next = array('label' => $this->getStatusLabel($statuses[$i]), 'code'=> $statuses[$i]);
                $options[] = array('label' => $this->getStatusLabel($statuses[$i]), 'code'=> $statuses[$i]);
                $founded = false;
            }
            if ($status == $statuses[$i]){
                $founded = true;
            }
        }

        if ($next) {
            $result['next'] = $next;
            $result['options'] = $options;
        } else {
            $result['next'] = null;
            $result['options'] = null;
        }

        return $result;

    }

    public function getStatusLabel($statusCode){
        $status = $this->_objectManager->create(\Magento\Sales\Model\Order\Status::class)->load($statusCode);
        if ($status) return $status->getLabel();
        return '';
    }
}