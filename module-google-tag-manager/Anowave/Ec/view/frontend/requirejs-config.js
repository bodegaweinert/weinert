var config = 
{
    config: 
    {
        mixins: 
        {
        	'Magento_Checkout/js/action/select-payment-method':
			{
				'Anowave_Ec/js/action/select-payment-method':true
			},
			'Magento_Checkout/js/action/select-shipping-method':
			{
				'Anowave_Ec/js/action/select-shipping-method':true
			},
			'Magento_Checkout/js/action/place-order': 
			{
			    'Anowave_Ec/js/action/place-order': true
			},
            'Magento_Checkout/js/model/step-navigator': 
            {
                'Anowave_Ec/js/step-navigator/plugin': true
            },
            'Magento_Checkout/js/sidebar':
            {
            	'Anowave_Ec/js/sidebar': true
            },
            'Magento_Catalog/js/price-box':
            {
            	'Anowave_Ec/js/price-box': true
            }
        }
    }
};