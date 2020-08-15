define(['jquery','mage/utils/wrapper', 'Magento_Checkout/js/model/quote'], function($, wrapper, quote)
{
    'use strict';

    return function(paymentMethod) 
    {
        return wrapper.wrap(paymentMethod, function (originalAction, method) 
        {
        	if ('undefined' !== typeof dataLayer && 'undefined' !== typeof data)	
        	{
	        	(function(dataLayer, $, paymentMethod)
	    		{
	    			/**
	        		 * Empty default payment method by default
	        		 */
	        		var method = '';
	        		
	        		if (paymentMethod && paymentMethod.hasOwnProperty('title'))
	        		{
	        			/**
	        			 * Set payment method
	        			 */
	        			method = paymentMethod.title;
	        		}
	        		else 
	        		{
	        			if (paymentMethod)
	        			{
		        			/**
		        			 * By default send payment method as code
		        			 */
		        			method = paymentMethod.method;
		        			
		        			/**
		        			 * Try to map payment method to user-friendly text representation
		        			 */
		        			if (paymentMethod.hasOwnProperty('method'))
		        			{
		        				var label = $('label[for="' + paymentMethod.method + '"]');
		        				
		        				if (label.length && label.find('>span').length > 0)	
		        				{
		        					method = label.find('>span').text();
		        				}
		        			}
	        			}
	        		}
	
	        		AEC.Checkout.stepOption(AEC.Const.CHECKOUT_STEP_PAYMENT, method);
	
	    		})(dataLayer, jQuery, method);
        	}
        	
            return originalAction(method);
        });
    };
});