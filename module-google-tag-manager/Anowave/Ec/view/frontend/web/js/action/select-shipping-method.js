define(['jquery','mage/utils/wrapper', 'Magento_Checkout/js/model/quote'], function($, wrapper, quote)
{
    'use strict';

    return function(shippingMethod) 
    {
        return wrapper.wrap(shippingMethod, function (originalAction, method) 
        {
        	if ('undefined' !== typeof dataLayer && 'undefined' !== typeof data)	
        	{
        		(function(dataLayer, jQuery, shippingMethod)
        		{
        			var method = '';
        			
        			if (shippingMethod && shippingMethod.hasOwnProperty('method_title'))
        	    	{
        	    		method = shippingMethod.method_title;
        	    	}

        			AEC.Checkout.stepOption(AEC.Const.CHECKOUT_STEP_SHIPPING, method);

        		})(dataLayer, jQuery, method);
        	}
        	
            return originalAction(method);
        });
    };
});