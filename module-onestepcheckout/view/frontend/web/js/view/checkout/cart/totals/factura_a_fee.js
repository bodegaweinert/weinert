define(
    [
        'Aheadworks_OneStepCheckout/js/view/checkout/summary/factura_a_fee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                var price = this.getRawValue();
                return (price != 0 && price!= null);
            }
        });
    }
);