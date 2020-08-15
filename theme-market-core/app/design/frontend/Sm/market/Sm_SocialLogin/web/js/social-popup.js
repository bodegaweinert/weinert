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
    'Magento_Customer/js/customer-data',
    'Sm_SocialLogin/magnific/popup'
], function ($, customerData) {
    "use strict";
    $.widget('socialLogin.socialPopup', {
        options: {
            popupWrap: '#sm-social-login',
            popupEffect: '',
            headerLink: '.header .links, .section-item-content .header.links',
            ajaxLoading: '#sm-social-login .ajax-loading',
            loadingClass: 'social-login-ajax-loading',
            errorMsgClass: 'message-error error message',
            successMsgClass: 'message-success success message',
            loginFormContainer: '.social-login-type.authentication',
            loginFormContent: '.social-login-type.authentication .social-login-form-wrap .block-content',
            loginForm: '#social-form-login',
            loginBtn: '#social-login-btn-login',
            forgotBtn: '#social-form-login .action.remind',
            createBtn: '#social-form-login .action.create',
            formLoginUrl: '',
            forgotFormContainer: '.social-login-type.forgot',
            forgotFormContent: '.social-login-type.forgot .block-content',
            forgotForm: '#social-form-password-forget',
            forgotSendBtn: '#social-form-password-forget .action.send',
            forgotBackBtn: '#social-form-password-forget .action.back',
            forgotFormUrl: '',
            createFormContainer: '.social-login-type.create',
            createFormContent: '.social-login-type.create .block-content',
            createForm: '#social-form-create',
            createAccBtn: '#social-form-create .action.create',
            createBackBtn: '#social-form-create .action.back',
            createFormUrl: '',
            loginCaptchaImg: '.authentication .captcha-img',
            createCaptchaImg: '.create .captcha-img',
            forgotCaptchaImg: '.forgot .captcha-img'
        },

        _create: function () {
            this._initHandlePointClick();
            this._initHandleBtnClik();
        },

        _initHandlePointClick: function () {
            var _self = this,
                _options = _self.options,
                _headerLink = $(_options.headerLink);
            if (_headerLink.length) {
                _headerLink.find('a').each(function (link) {
                    var that = $(this),
                        _href = that.attr('href'),
                        _createExist = _href.search('customer/account/create') != -1 ? true : false,
                        _loginExist = _href.search('customer/account/login') != -1 ? true : false;
                    if (typeof _href !== 'undefined' && (_createExist || _loginExist)) {
                        that.addClass('social-login');
                        that.attr('data-mfp-src', _options.popupWrap);
                        that.attr('data-effect', _options.popupEffect);
                        that.off('click').on('click', function (e) {
                            e.preventDefault();
                            if (_createExist) {
                                _self._displayCreate();
                            } else {
                                _self._displayLogin();
                            }
                            $("html").removeClass('nav-before-open nav-open');
                        });
                    }
                });
                _self._initMagnificPopup();
            }
        },

        _initMagnificPopup: function () {
            var _self = this,
                _options = _self.options;
            $(_options.headerLink).magnificPopup({
                delegate: 'a.social-login',
                removalDelay: 500,
                closeMarkup: '<div class="mfp-close">&#215;</div>',
                callbacks: {
                    beforeOpen: function () {
                        this.st.mainClass = this.st.el.attr('data-effect');
                    }
                },
                midClick: true
            });
        },

        _initHandleBtnClik: function () {
            var _self = this,
                _options = _self.options,
                _loginForm = $(_options.loginForm),
                _createForm = $(_options.createForm),
                _forgotForm = $(_options.forgotForm);

            $(_options.loginBtn).off('click').on('click', _self._processLogin.bind(_self));
            $(_options.createBtn).off('click').on('click', _self._displayCreate.bind(_self));
            $(_options.forgotBtn).off('click').on('click', _self._displayForgot.bind(_self));
            $(_options.createAccBtn).off('click').on('click', _self._processCreate.bind(_self));
            $(_options.createBackBtn).off('click').on('click', _self._displayLogin.bind(_self));
            $(_options.forgotSendBtn).off('click').on('click', _self._processForgot.bind(_self));
            $(_options.forgotBackBtn).off('click').on('click', _self._displayLogin.bind(_self));

            _loginForm.find('input').off('keypress').on('keypress', function (e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    _self._processLogin();
                }
            });

            _createForm.find('input').off('keypress').on('keypress', function (e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    _self._processCreate();
                }
            });

            _forgotForm.find('input').off('keypress').on('keypress', function (e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    _self._processForgot();
                }
            });
        },

        _appendLoading: function (_html) {
            var _self = this,
                _options = _self.options;
            _html.css('position', 'relative');
            _html.prepend($("<div></div>", {
                "class": _options.loadingClass
            }))
        },

        _removeLoading: function (_html) {
            var _self = this,
                _options = _self.options;
            _html.css('position', '');
            _html.find("." + _options.loadingClass).remove();
        },

        _addMsg: function (_html, message, messageClass) {
            if (typeof(message) === 'object' && message.length > 0) {
                message.forEach(function (msg) {
                    this._appendMessage(_html, msg, messageClass);
                }.bind(this));
            } else if (typeof(message) === 'string') {
                this._appendMessage(_html, message, messageClass);
            }
        },

        _removeMsg: function (_html, messageClass) {
            _html.find('.' + messageClass.replace(/ /g, '.')).remove();
        },

        _appendMessage: function (_html, message, messageClass) {
            var currentMessage = null;
            var messageSection = _html.find("." + messageClass.replace(/ /g, '.'));
            if (!messageSection.length) {
                _html.prepend($('<div></div>', {
                    'class': messageClass
                }));
                currentMessage = _html.children().first();
            } else {
                currentMessage = messageSection.first();
            }
            currentMessage.append($('<div>' + message + '</div>'));
        },

        _displayLogin: function () {
            var _self = this,
                _options = _self.options;
            $(_options.loginFormContainer).show();
            $(_options.forgotFormContainer).hide();
            $(_options.createFormContainer).hide();
        },

        _processLogin: function () {
            var _self = this,
                _options = _self.options,
                _loginForm = $(_options.loginForm),
                _loginFormContent = $(_options.loginFormContent),
                _loginCaptchaImg = $(_options.loginCaptchaImg);
            if (!_loginForm.valid()) {
                return;
            }

            var _data = {},
                formDataArr = _loginForm.serializeArray();

            formDataArr.forEach(function (entry) {
                _data[entry.name] = entry.value;
            });

            _self._appendLoading(_loginFormContent);
            _self._removeMsg(_loginFormContent, _options.errorMsgClass);

            $.ajax({
                url: _options.formLoginUrl,
                type: 'POST',
                data: JSON.stringify(_data)
            }).done(function (response) {
                if (response.errors) {
                    _self._removeLoading(_loginFormContent);
                    if (response.imgSrc) {
                        if (_loginCaptchaImg.length) {
                            _self._addMsg(_loginFormContent, response.message, _options.errorMsgClass);
                            _loginCaptchaImg.attr('src', response.imgSrc);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        _self._addMsg(_loginFormContent, response.message, _options.errorMsgClass);
                    }
                } else {
                    customerData.invalidate(['customer']);
                    _self._addMsg(_loginFormContent, response.message, _options.successMsgClass);
                    if (response.redirectUrl) {
                        window.location.href = response.redirectUrl;
                    } else {
                        location.reload();
                    }
                }
            }).fail(function () {
                _self._removeLoading(_loginFormContentt);
                _self._addMsg(_loginFormContent, $.mage.__("Could not authenticate. Please try again later"), _options.errorMsgClass);
            });
        },

        _displayForgot: function () {
            var _self = this,
                _options = _self.options;
            $(_options.loginFormContainer).hide();
            $(_options.forgotFormContainer).show();
            $(_options.createFormContainer).hide();
        },

        _processForgot: function () {
            var _self = this,
                _options = _self.options,
                _forgotForm = $(_options.forgotForm),
                _forgotFormContent = $(_options.forgotFormContent),
                _forgotCaptchaImg = $(_options.forgotCaptchaImg);
            if (!_forgotForm.valid()) {
                return;
            }

            var parameters = _forgotForm.serialize();
            this._appendLoading(_forgotFormContent);
            this._removeMsg(_forgotFormContent, _options.errorMsgClass);
            this._removeMsg(_forgotFormContent, _options.successMsgClass);

            $.ajax({
                url: _options.forgotFormUrl,
                type: 'POST',
                data: parameters
            }).done(function (response) {
                _self._removeLoading(_forgotFormContent);
                if (response.success) {
                    _self._addMsg(_forgotFormContent, response.message, _options.successMsgClass);
                } else {
                    _self._addMsg(_forgotFormContent, response.message, _options.errorMsgClass);
                }
                if (response.imgSrc && _forgotCaptchaImgg.length) {
                    _forgotCaptchaImg.attr('src', response.imgSrc);
                }
            });
        },

        _displayCreate: function () {
            var _self = this,
                _options = _self.options;
            $(_options.loginFormContainer).hide();
            $(_options.forgotFormContainer).hide();
            $(_options.createFormContainer).show();
        },

        _processCreate: function () {
            var _self = this,
                _options = _self.options,
                _createForm = $(_options.createForm),
                _createFormContent = $(_options.createFormContent),
                _createCaptchaImg = $(_options.createCaptchaImg);
            if (!_createForm.valid()) {
                return;
            }
            var _data = _createForm.serialize();
            this._appendLoading(_createFormContent);
            this._removeMsg(_createFormContent, _options.errorMsgClass);

            $.ajax({
                url: _options.createFormUrl,
                type: 'POST',
                data: _data
            }).done(function (response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else if (response.success) {
                    customerData.invalidate(['customer']);
                    _self._addMsg(_createFormContent, response.message, _options.successMsgClass);
                    window.location.reload(true);
                } else {
                    _self._removeLoading(_createFormContentt);
                    if (response.imgSrc) {
                        if (_createCaptchaImg.length) {
                            _self._addMsg(_createFormContent, response.message, _options.errorMsgClass);
                            _createCaptchaImg.attr('src', response.imgSrc);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        _self._addMsg(_createFormContent, response.message, _options.errorMsgClass);
                    }
                }
            });
        }

    });

    return $.socialLogin.socialPopup;
});