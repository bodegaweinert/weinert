define(['jquery','mage/utils/wrapper'], function ($, wrapper) 
{
    'use strict';

    return function (placeOrderAction) 
    {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) 
        {
        	if ('undefined' !== typeof data)
        	{
        		/**
        		 * Set step
        		 */
        		data.ecommerce.checkout.actionField.step = AEC.Const.CHECKOUT_STEP_ORDER;
        		
        		if (AEC.Const.COOKIE_DIRECTIVE)
				{
					if (AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED)
					{
		        		dataLayer.push(data);
					}
				}
        		else 
        		{
        			dataLayer.push(data);
        		}
        		
        	}

            return originalAction(paymentData, messageContainer);
        });
    };
});