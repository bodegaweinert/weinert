define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _loadMedia: function () {
                var productId = this.getProduct(),
                    imageContainer = null;
                if (!this.labels) {
                    this.labels = [];
                }
                if (this.inProductList) {
                    imageContainer = this.element.closest('li.item').find(this.options.jsonConfig['label_category']);
                } else {
                    imageContainer = this.element.closest('.column.main').find(this.options.jsonConfig['label_product']);
                }
                if (typeof this.options.jsonConfig['label_reload'] != 'undefined'
                    && this.labels.indexOf(productId) === -1
                    && (!this.inProductList || this.options.jsonConfig['original_product_id'] != productId)
                ) {
                    this.labels.push(productId);
                    $.ajax({
                        url: this.options.jsonConfig['label_reload'],
                        data: {
                            product_id: productId,
                            in_product_list: this.inProductList ? 1 : 0
                        },
                        method: 'GET',
                        cache: true,
                        dataType: 'json',
                        showLoader: false
                    }).done(function (data) {
                        if (data.labels) {
                            imageContainer.last().after(data.labels);
                        }
                    });
                }
                imageContainer.find('.amasty-label-container').hide();
                imageContainer.find('.amasty-label-for-' + productId).show();

                return this._super();
            }
        });

        return $.mage.SwatchRenderer;
    }
});
