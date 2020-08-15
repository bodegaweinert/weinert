define([
    'jquery',
    'mage/url'
], function ($, url) {
    'use strict';

    $.widget('mage.customerLogin', {
        options: {
            userEmail          : "#useremail",
            userPassword       : "#userpass",
            sendPasswordButton : '#send_new_password',
            checkGif           : '.image-check',
            checkGifSrc        : '/media/onestepcheckout/check-ok-jse.gif',
            loader             : '#login-loader'
        },

        _create:function(){
            this._bind();
        },

        _bind:function(){
            var self = this;

            $(this.options.sendPasswordButton).click(function(event){
                self._handleSendPasswordButtonClick(event);
            });

            $(this.options.userEmail).keyup(function(event){
                self._handleInputKeyUp(event);
            });
            $(this.options.userPassword).keyup(function(event){
                self._handleInputKeyUp(event);
            });
        },

        _handleInputKeyUp: function(event){
            var elem = $(event.currentTarget);
            if (elem.hasClass('mage-error')){
                elem.removeClass('mage-error').parent().find('[generated]').remove();
            }
        },

        _handleSendPasswordButtonClick: function(event){
            event.stopPropagation();
            event.stopImmediatePropagation();

            var self = this;

            if (this._validate()){
                self._showLoader();

                $.post(
                    url.build('onestepcheckout/customer/sendNewPassword'),
                    {customer : $(this.options.userEmail).val()}
                ).done(
                    function (response) {
                        self._hideLoader();

                        var passwordSentBlock = $('.password-sent');
                        var span = $('.password-sent span');

                        span.css('opacity',0);

                        $(self.options.checkGif).attr('src','');

                        passwordSentBlock.show();

                        $(self.options.checkGif).attr('src', self.options.checkGifSrc.replace(/\?.*$/,"")+"?x="+Math.random());

                        setTimeout(function(){
                            span.animate({ opacity: "1" }, 2000 );
                        },1000);

                        setTimeout(function(){
                            passwordSentBlock.slideToggle(2000);
                        },10000);

                    }.bind(this)
                ).fail(
                    function (response) {
                        self._hideLoader();
                    }.bind(this)
                );


                console.log('MANDO PASSWORD');

                //this._sendPassword();

            }

            return false;

        },

        /**
        * Validate email
        */
        _validate: function(){
            var form = $('#login-form');

            form.validation('isValid');

            var password =  $(this.options.userPassword);
            if (password.hasClass('mage-error')) {
                password.removeClass('mage-error').parent().find('[generated]').remove();
            }

            var email = $(this.options.userEmail);
            if (email.hasClass('mage-error')){
                return false;
            } else {
                return true;
            }
        },

        _showLoader: function () {
            $(this.options.loader).show();
        },
        _hideLoader: function(){
            $(this.options.loader).hide();
        },

    });

    return $.mage.customerLogin;
});