define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('system.message', {
        options: {
            mainContainer   : '.cmb-system-messages'
        },

        _create:function(){
            this._bind();
        },

        _bind: function(){
            var self = this;

            $('div[data-bind="scope: \'messages\'"]').on('DOMSubtreeModified', function(){
                self._checkForMessages();
            });

            $(document).bind('showSystemMessage',function(event, messageType, message){
                self._showMessage(messageType, message);
            });
        },

        _showMessage: function(messageType, message){
            var self = this;

            var messageContainer = $('<div>',{
                id: 'cmb_message_' + String(Math.random()).replace('.',''),
                'class' : 'cmb-message'
            });

            var closeButton = $('<button>',{
                type : 'button',
                'class' : 'btn-close',
                html : '<span>Cerrar</span>'
            });

            var messageElement = $('<p>',{
               html : message
            });

            messageContainer.addClass(messageType);
            messageContainer.append(closeButton);
            closeButton.click(function(){
                self._hideMessage(messageContainer.attr('id'));
            });
            messageContainer.append(messageElement);

            messageContainer.css('margin-bottom','-50px');


            $(this.options.mainContainer).append(messageContainer);

            messageContainer.animate({'margin-bottom':'25px'}, 500);

            if (messageType != 'error') {
                setTimeout(function(){
                    self._hideMessage(messageContainer.attr('id'));
                },10000);
            }

        },

        _hideMessage: function(idMessage){
            var element = $('#' + idMessage);
            if (element.length){
                element.animate({'opacity':'0'}, 500, function() {
                    element.remove();
                });
            }
        },

        _checkForMessages: function(){
            var self = this;

            if (jQuery('.messages .message').length){
                jQuery('.messages .message').each(function(index, element){
                    var elem = jQuery(element);
                    var elemClass = elem.attr('class');
                    var message = elem.text().trim();
                    var messageClass = '';

                    if (elemClass.indexOf('error') > -1){
                        messageClass = 'error';
                    }

                    if (elemClass.indexOf('alert') > -1) {
                        messageClass = 'alert';
                    }

                    if (elemClass.indexOf('warning') > -1) {
                        messageClass = 'warning';
                    }

                    if (elemClass.indexOf('notice') > -1) {
                        messageClass = 'notice';
                    }

                    if (elemClass.indexOf('success') > -1) {
                        messageClass = 'success';
                    }

                    if (message != '') {
                        self._showMessage(messageClass, message);
                        elem.remove();
                    }
                });
            }
        }
    });
    return $.system.message;
});