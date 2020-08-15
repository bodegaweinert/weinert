/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    window.socialLoginCallback = function (url, windowObj) {
        customerData.invalidate(['customer']);
        customerData.reload(['customer'], true);
        if (typeof url !== 'undefined' && url !== '') {
            window.location.href = url;
        } else {
            window.location.reload(true);
        }
        windowObj.close();
    };

    return function (config, element) {
        var model = {
            initialize: function () {
                var _self = this;
                $(element).off('click').on('click', function () {
                    _self._openPopup();
                });
            },

            _openPopup: function () {
                window.open(config.url, config.label, this._getParams());
            },

            _getParams: function (w, h, l, t) {
                this.screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
                this.screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
                this.outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
                this.outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
                this.width = w ? w : 600;
                this.height = h ? h : 500;
                this.left = l ? l : parseInt(this.screenX + ((this.outerWidth - this.width) / 2), 10);
                this.top = t ? t : parseInt(this.screenY + ((this.outerHeight - this.height) / 2.5), 10);
                return (
                    'width=' + this.width +
                    ',height=' + this.height +
                    ',left=' + this.left +
                    ',top=' + this.top
                );
            }
        };
        model.initialize();
        return model;
    };
});