define(
    [
        'MercadoPago_Core/js/view/checkout/summary/finance_cost'
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