define([
    'jquery',
    'mage/url'
], function ($, url) {
    'use strict';

    $.widget('mage.awOscAuthenticationPopup', {
        options: {
            triggerButton : '#authPopupLink',
            modalDiv : '.block-authentication',
            buttons: 'button.cmb-form',
            authDiv: '.cmb-login-form',
            currentContentShown : '.selected .content',
            passwordInput : '.cmb-login-form .field-password input',
            retryPassword : '.content-footer',
            usernameInput : '#login-email',
            username: '#customer-email',
            closeButton : '.modal-header button.action-close',
            checkGif : '.image-check',
            checkGifSrc : '/media/onestepcheckout/check-ok-jse.gif'
        },

        _create:function(){
            this._bind();
        },

        _bind:function(){
            var self = this;

            $(this.options.triggerButton).click(function(){
                $(self.options.modalDiv).modal('openModal');
            });

            $(this.options.buttons).click(function(event){
                self._handleButtonClickEvent(event);
            });
            $(this.options.passwordInput).keyup(function(){
                self._handlePasswordChange();
            });
            $(this.options.retryPassword).click(function(){
                self._resendPassword();
            });

            $(this.options.closeButton).click(function(){
                self._handleCloseButtonClick();
            });

            $('.action.back-to-options').click(function(){
                $('.popup-content').animate({'margin-left':'360px'},1000,function(){
                    $('.content .content-title').css('display','none');
                    $('.content .content-footer').css('display','none');
                });
                $('.regular-password-login').removeClass('selected');
                $('.new-password-login').removeClass('selected');
            });
        },
        _handleCloseButtonClick: function () {
            $('.popup-content').css('margin-left','360px');
            $('.regular-password-login').removeClass('selected');
            $('.new-password-login').removeClass('selected');
            $('.content .content-title').css('display','none');
            $('.content .content-footer').css('display','none');
        },

        _resendPassword:function(){
            this._sendPassword(true);
        },

        _handlePasswordChange: function(){
            var elem = $(this.options.passwordInput);
            if (elem.hasClass('mage-error')){
                elem.removeClass('mage-error').parent().find('[generated]').remove();
            }
        },

        _handleButtonClickEvent: function(event){
            var target = $(event.currentTarget);
            var parent = $(target.parent()[0]);
            var content = $(parent.children('.content'));

            if (!parent.hasClass('selected')){

                $('.regular-password-login').removeClass('selected');
                $('.new-password-login').removeClass('selected');

                parent.addClass('selected');

                $('.popup-content').animate({'margin-left':'-360px'},1000);

                if (parent.hasClass('new-password-login')){
                    $('.login-form .general-subtitle').html('Quiero recibir la contraseña por mail');

                    this._sendPassword();
                    var span = $('.content-title span');
                    var self = this;

                    $('.content .content-title').css('display','block');
                    $('.content .content-footer').css('display','block');

                    span.css('opacity',0);
                    $(this.options.checkGif).attr('src','');

                    setTimeout(function(){
                        $(self.options.checkGif).attr('src', self.options.checkGifSrc.replace(/\?.*$/,"")+"?x="+Math.random());
                    },1000);

                    setTimeout(function(){
                        span.animate({ opacity: "1" }, 1000 );
                    },3000);

                } else {
                    $('.login-form .general-subtitle').html('Conozco mi contraseña');
                    $('.content .content-title').css('display','none');
                    $('.content .content-footer').css('display','none');
                }

            }
        },

        /**
         * Send new password
         */
        _sendPassword: function(customerResend){
            self = this;
            $(this.options.usernameInput).val($(this.options.username).val());

            if (customerResend){
                $(self.options.retryPassword).removeClass('loading').addClass('loading');
            }

            $.post(
                url.build('onestepcheckout/customer/sendNewPassword'),
                {customer : $(this.options.usernameInput).val()}
            ).done(
                function (response) {
                    if (customerResend){
                        $(self.options.retryPassword).removeClass('loading').addClass('ok');
                        setTimeout(function(){
                            $(self.options.retryPassword).removeClass('ok');
                        },3000);
                    }
                }.bind(this)
            ).fail(
                function (response) {

                }.bind(this)
            );
        }
    });

    return $.mage.awOscAuthenticationPopup;
});