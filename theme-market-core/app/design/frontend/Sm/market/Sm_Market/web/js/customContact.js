define([
    'jquery',
    'mage/url'
], function ($, urlManager) {
    'use strict';

    $.widget('custom.contact', {
        options: {
            messageContainer: '.contact-message',
            contactForm: '#contact-form',
            contactLoader: '#contact-loader',
            inputs: '#contact-form input.input-text',
            textarea: '#contact-form #comment',
            buttonContainer: '.actions-toolbar .primary'
        },

        _create:function(){
            this._bind();
        },

        _bind: function(){
            var self = this;

            $(self.options.contactForm).submit(function (event) {
                event.preventDefault();
                if($(this).valid()){
                    self._postContact();
                }
            });

        },


        _showLoader: function(){
            $(this.options.buttonContainer).css('display','none');
            $(this.options.contactLoader).css('display','block');
        },

        _hideLoader: function(){
            $(this.options.contactLoader).css('display','none');
            $(this.options.buttonContainer).css('display','block');
        },

        _showMessage: function(){
            var self = this;
            $(this.options.messageContainer).slideToggle(500);
            setTimeout(function(){
                $(self.options.messageContainer).slideToggle(500);
            },4000);

        },

        _postContact:function(){
            var self = this;
            var _url = urlManager.build('market/contact/post');
            var _form_data = $(this.options.contactForm).serialize();

            $.ajax({
                url: _url,
                data: _form_data,
                method: "post",
                dataType: "json",
                beforeSend: function (){
                    self._showLoader();
                },
                success: function (res) {},
                error: function () {
                    console.log('error');
                }
            }).done(function(resp){
                self._hideLoader();
                if (resp.success == true) {
                    self._showMessage();
                    $(self.options.contactForm)[0].reset();
                }
            });
        }

    });
    return $.custom.contact;
});