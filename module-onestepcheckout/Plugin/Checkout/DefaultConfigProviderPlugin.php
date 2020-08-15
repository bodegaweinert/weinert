<?php
/**
 * Created by PhpStorm.
 * User: nahuel
 * Date: 12/05/18
 * Time: 13:07
 */

namespace Aheadworks\OneStepCheckout\Plugin\Checkout;


class DefaultConfigProviderPlugin
{
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ){
        $data = $result;

        if (isset($data['customerData']['addresses'])){
            $data['customerData']['addresses'] = $this->_removeDuplicateAddresses($data['customerData']['addresses']);
        }

        return $data;
    }

    private function _removeDuplicateAddresses($addresses){
        $originalAddresses = $addresses;
        $addressCount = count($addresses);

        /*for($i=0;$i<$addressCount;$i++){
            unset($addresses[$i]['id']);
            unset($addresses[$i]['default_shipping']);
            unset($addresses[$i]['default_billing']);
        }

        for($i=1; $i< $addressCount; $i++){
            if ($addresses[$i] == $addresses[$i-1]){
                unset($originalAddresses[$i]);
            }
        }*/
        for ($i = 0; $i < $addressCount; $i++){
            if (isset($addresses[$i]['default_billing']) && $addresses[$i]['default_billing'] == true) {
                array_splice($originalAddresses,$i,1);
            }
        }

        return $originalAddresses;
    }


}