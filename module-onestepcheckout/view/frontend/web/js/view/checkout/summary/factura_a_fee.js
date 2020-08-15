define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/checkout/summary/factura_a_fee'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode();
            },

            getRawValue: function () {
                var price = 0;
                if (this.totals() && totals.getSegment('factura_a_fee')) {
                    price = totals.getSegment('factura_a_fee').value;
                }
                return price;
            },

            getValue: function() {
                var price = this.getRawValue();
                return this.getFormattedPrice(price);
            },
        });
    }
);