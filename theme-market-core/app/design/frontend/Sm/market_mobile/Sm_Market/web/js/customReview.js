define([
    'jquery',
    'mage/url',
    'mage/validation'
], function ($, urlManager,validator) {
    'use strict';

    $.widget('custom.review', {
        options: {
            messageContainer: '.block.review-add .block-content .review-message',
            reviewForm: '.block.review-add .block-content #review-form',
            reviewLoader: '.block.review-add .block-content #review-loader',
            inputs: '#review-form input.input-text',
            textarea: '#review-form #review_field',
            errors: '#review-form .mage-error',
            bindingApplied : false,
            tabButton : '#tab-label-reviews-title',
            ratingErrorMessage: '#ratings-error',
            buttonContainer: '.review-form-actions'
        },

        _create:function(){
            this._bind();
        },

        _bind: function(){
            var self = this;

            self._applyBindings(self);
        },

        _applyBindings: function(self){
            if (!self.options.bindingApplied) {
                self._captureProductId();

                $(self.options.reviewForm).submit(function (event) {
                    self._clearErrorMessages($(self.options.inputs));
                    self._clearErrorMessages($(self.options.textarea));
                    $(self.options.ratingErrorMessage).removeClass('mage-error').css('display','none');

                    event.preventDefault();

                    if ($(event.target).valid()) {
                        if(self._validateRating()){
                            self._postReview();
                        }
                    }else{
                        self._validateRating();
                    }
                });

                self.options.bindingApplied = true;
            }
        },

        _captureProductId : function(){
            var form = $(this.options.reviewForm);
            var action = form.attr('action');

            var productId = action.split('/id/')[1].replace('/','');

            var hidden = $('<input>',{type:'hidden', name:'id', value: productId});
            hidden.appendTo(form);

            form.removeAttr('action');

        },

        _clearErrorMessages : function(event){
            var elem = event;
            if (elem.hasClass('mage-error')){
                elem.removeClass('mage-error').parent();
            }
        },

        _showLoader: function(){
            $(this.options.buttonContainer).css('display','none');
            $(this.options.reviewLoader).css('display','block');
        },

        _hideLoader: function(){
            $(this.options.reviewLoader).css('display','none');
            $(this.options.buttonContainer).css('display','block');
        },

        _showMessage: function(){
            var self = this;
            $(this.options.messageContainer).slideToggle(500);
            setTimeout(function(){
                $(self.options.messageContainer).slideToggle(500);
            },4000);

        },

        _validateRating:function () {
            if (!$('.radio:checked').length){
                $(this.options.ratingErrorMessage).addClass('mage-error').css('display','block');
                return false;
            }else{
                return true;
            }
        },

        _postReview:function(){
            var self = this;
            var _url = urlManager.build('market/review/post');
            var _form_data = $(this.options.reviewForm).serialize();

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
                    $(self.options.reviewForm)[0].reset();
                }
            });
        }

    });
    return $.custom.review;
});