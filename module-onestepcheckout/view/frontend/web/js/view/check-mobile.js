define([
    'jquery',
    'mage/url'
], function ($, url) {
    'use strict';
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';

    $.widget('mage.awOscCheckMobile', {
        options: {
            fields: [
                {elem: '.field.fl-auth-message', dir: DIRECTION_DOWN},
                {elem: '.field.street', dir: DIRECTION_UP},
                {elem: '.fl-label[name="shippingAddress.city"]',     dir: DIRECTION_UP},
                {elem: '.fl-label[name="shippingAddress.postcode"]', dir: DIRECTION_DOWN}
            ],
            optionalFields: [
                {elem: '.field.require-invoice-chk', dir: DIRECTION_UP},
                {elem: '.fl-label[name="shippingAddress.custom_attributes.business_name"]', dir: DIRECTION_DOWN}
            ]
        },

        _create:function(){
            this._bind();
        },

        _bind:function(){
            var self = this;
            $(document).ready(function() {
                if (self._isMobile()) {
                    self._checkAndUpdateHtml(self);
                }
            });
        },

        _checkAndUpdateHtml:function(self){
            var elements = self.options.fields;

            if ($('.field.require-invoice-chk').length){
                elements = self.options.fields.concat(self.options.optionalFields);
            }

            for (var i = 0; i < elements.length; i++){
                var element = elements[i];
                if ($(element.elem).length){
                    if (! $(element.elem).hasClass('mb-moved')){
                        self._updateHtml(element.elem, element.dir);
                    }
                }
            }

            if ($('.mb-moved').length != elements.length){
                setTimeout(self._checkAndUpdateHtml, 100, self);
            }

        },


        _updateHtml:function(element, direction){
            var elem = $(element);
            var row =  elem.parent('.field-row'); //div que tiene field-row de class
            var newRow = $('<div>',{'class':'field-row'});

            elem.appendTo(newRow);

            if (direction == DIRECTION_UP) {
                newRow.insertBefore(row);
            }

            if (direction == DIRECTION_DOWN){
                newRow.insertAfter(row);
            }

            elem.addClass('mb-moved');
        },

        _isMobile: function(){
            return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
        },

    });

    return $.mage.awOscCheckMobile;
});