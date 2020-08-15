define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Catalog/js/catalog-add-to-cart'
], function($, $t) {
    "use strict";
    $.widget('mage.customAjax', $.mage.catalogAddToCart, {

        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            // setting global variable required for customized ajax cart process

            window.ajaxCartTransport = false;

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');


                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }

                    self.enableAddToCartButton(form);

                    // changing global variable value to true (this flag enables communication with update minicart logic)

                    window.ajaxCartTransport = true;
                }
            });
        }
    });

    return $.mage.customAjax;
});