define([
    'Magento_Ui/js/form/element/single-checkbox',
    'mage/translate',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/cache',
    'Aheadworks_OneStepCheckout/js/action/set-shipping-information',
    'jquery'
], function (AbstractField, $t, totalsProvider, quote, cartCache, setShippingInformationAction, $) {
    'use strict';

    return AbstractField.extend({
        defaults: {
            default: false,
            modules: {
                businessName: '${ $.parentName }.business_name',
                cuit: '${ $.parentName }.cuit',
                convenio: '${ $.parentName }.convenio'
            }
        },

        initialize: function(){
            this._super();
            this.updateBusinessName();
            this.updateCuit();
            this.updateConvenio();
        },

        updateBusinessName: function () {
            if (this.businessName()){
                if (this.value()) {
                    this.businessName().visible(true);
                } else {
                    this.businessName().visible(false);
                }
            }
        },

        updateCuit: function () {
            if (this.value()) {
                this.cuit().visible(true);
            } else {
                this.cuit().visible(false);
            }
        },

        updateConvenio: function () {
            if(this.convenio()){
                if($('select[name=custom_attributes\\[convenio\\]]').val() != undefined){
                    $('select[name=custom_attributes\\[convenio\\]]').bind('change',this.updateamount);
                }
                if (this.value()) {
                    this.convenio().visible(true);
                } else {
                    this.convenio().visible(false);
                }
            }
        },

        onCheckedChanged: function () {
            this.updateBusinessName();
            this.updateCuit();
            this.updateConvenio();
            this.updateamount();
        },

        onExtendedValueChanged: function () {
            this.updateBusinessName();
            this.updateCuit();
            this.updateConvenio();
            this.updateamount();
        },

        updateamount:function () {
            $(document).trigger('forceReloadMP');
            if(quote.shippingMethod() != null){
                setShippingInformationAction();
            }
            cartCache.set('totals',null);
            var totals = totalsProvider.estimateTotals(quote.shippingAddress());
            return totals;
        }
    });
});