/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'socialProvider'
    ],
    function ($, ko, Component, socialProvider) {
        'use strict';

        ko.bindingHandlers.socialLoginButton = {
            init: function (element, valueAccessor, allBindings) {
                var config = {
                    url: allBindings.get('url'),
                    label: allBindings.get('label')
                };
                socialProvider(config, element);
            }
        };

        return Component.extend({
            defaults: {
                template: 'Sm_SocialLogin/social-buttons'
            },
            buttonLists: window.socialAuthenticationPopup,
            socials: function () {
                var socials = [];

                $.each(this.buttonLists, function (key, social) {
                    socials.push(social);
                });

                return socials;
            },

            isActive: function () {
                return (typeof this.buttonLists !== 'undefined');
            }
        });
    }
);
