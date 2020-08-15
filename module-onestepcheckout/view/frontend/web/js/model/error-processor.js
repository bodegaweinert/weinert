/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url',
    'Magento_Ui/js/model/messageList',
    'jquery',
    'Magento_Ui/js/modal/modal'
], function (fullScreenLoader, url, globalMessageList) {
    'use strict';

    return {
        /**
         * @param {Object} response
         * @param {Object} messageContainer
         */
        process: function (response, messageContainer) {
            var error;

            messageContainer = messageContainer || globalMessageList;

            if (response.status == 401) { //eslint-disable-line eqeqeq
                window.location.replace(url.build('customer/account/login/'));
            } else {
                error = JSON.parse(response.responseText);

                var element = jQuery('<div class="modal-payment-error" style="font-size:16px">' + error.message + '</div>'),
                    modal = element.modal({buttons: [{
                        text: 'Entendido',
                        class: 'modal-error-button',
                        click: function() {
                            this.closeModal();
                        }
                    }]}).data('mage-modal');

                fullScreenLoader.stopLoader();
                element.trigger('openModal');
            }
        }
    };
});
