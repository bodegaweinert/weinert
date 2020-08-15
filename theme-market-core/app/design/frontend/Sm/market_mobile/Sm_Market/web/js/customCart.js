define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Customer/js/customer-data',
    'mage/url'
], function ($,quote, totalsDefaultProvider, customerData, urlManager) {
    'use strict';

    $.widget('custom.cart', {
        options: {
            qtyPlusButton: '.cart-container .quantity-plus',
            qtyMinusButton: '.cart-container .quantity-minus',
            deleteItemButton: '.cart-container .button-delete-item',
            wishlistItemButton: '.cart-container .button-wishlist-item',
            cartLoader: '#cart-loader',
            messagesContainer: '#cart-messages.messages .message',
            qtyInputs : '.cart-container [data-role="cart-item-qty"]'
        },

        _create:function(){
            this._bind();
        },

        _bind: function(){
            var self = this;

            $(this.options.qtyPlusButton).bind('click',function (event) {
               self._qtyPlusButtonClick(event);
            });

            $(this.options.qtyMinusButton).bind('click',function (event) {
                self._qtyMinusButtonClick(event);
            });

            $(this.options.deleteItemButton).bind('click',function (event) {
                self._deleteButtonClick(event);
            });

            $(this.options.qtyInputs).bind('change',function(){
                self._updateCart();
            });

            $(this.options.wishlistItemButton).bind('click', function(event){
                self._addToWishList(event);
            })

        },

        _unbind: function(){

            $(this.options.qtyPlusButton).unbind('click');

            $(this.options.qtyMinusButton).unbind('click');

            $(this.options.deleteItemButton).unbind('click');

            $(this.options.qtyInputs).unbind('change');

            $(this.options.wishlistItemButton).unbind('click');
        },

        _showLoader: function(){
            $(this.options.cartLoader).css('display','block');
        },

        _hideLoader: function(){
            $(this.options.cartLoader).css('display','none');
        },

        _clearMessages: function(){
            $('#cart-messages').css('display','none');
            $(this.options.messagesContainer).removeClass('message-success').removeClass('success').removeClass('message-error').removeClass('error').children('div').html('');
        },

        _addMessage: function(message, type){
            $(this.options.messagesContainer).addClass('message-'+type).addClass(type).children('div').html(message);
            $('#cart-messages').css('display','block');
            if (type != 'error') {
                setTimeout(function(){
                    $('#cart-messages').slideToggle(1000);
                },4000);
            }
        },

        _qtyPlusButtonClick: function (event) {
            event.preventDefault();

            var item = $(event.target).parents('.control-qty-cart').children('input');
            var itemId = $(item).attr('id');
            $('#'+itemId).val(Number($('#'+itemId).val())+1);

            var priceCol = $($(event.target).parents('.item-info').find('span.price')[0]);
            var subtotalCol = $($(event.target).parents('.item-info').find('span.price')[1]);

            var price = Number(priceCol.html().replace('$','').replace(',','.'));
            var subtotal = (price * Number($('#'+itemId).val())).toFixed(2);

            subtotalCol.html('$'+ subtotal.toString().replace('.',','));

            this._updateCart();
            return false;
        },

        _qtyMinusButtonClick: function (event) {
            event.preventDefault();

            var item = $(event.target).parents('.control-qty-cart').children('input');
            var itemId = $(item).attr('id');

            var value = Number($('#'+itemId).val())-1;
            if(value > 0){
                $('#'+itemId).val(value);
                var priceCol = $($(event.target).parents('.item-info').find('span.price')[0]);
                var subtotalCol = $($(event.target).parents('.item-info').find('span.price')[1]);

                var price = Number(priceCol.html().replace('$','').replace(',','.'));
                var subtotal = (price * Number($('#'+itemId).val())).toFixed(2);

                subtotalCol.html('$'+ subtotal.toString().replace('.',','));

                this._updateCart();
            }

            return false;
        },

        _deleteButtonClick :function (event){
            event.preventDefault();

            var self = this;
            var item = $(event.target);
            var _data = JSON.parse(item.attr('data-post-url'));
            var _url = _data.action.replace('checkout','market');

            _data.data.form_key = $("input[name='form_key']").val();
            _data.data.isCheckoutPage = true;

            $.ajax({
                method: "post",
                url: _url,
                data: _data.data,
                dataType: "json",
                beforeSend: function (){
                    self._showLoader();
                    self._clearMessages();
                },
                success: function (resp) {}
            }).done(function(resp){

                if (resp.success == true) {
                    var newContent = $(resp.content);

                    $('[data-block="minicart"]').trigger('contentLoading');
                    $('form.form.form-cart',$('.cart-container')).replaceWith(newContent.html());

                    //cambio el data_id de customerData como para que se fuerze el update.
                    customerData.get('cart')()['data_id'] = Number(customerData.get('cart')()['data_id'] + 1);
                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                    self._controlCheckoutMethodsBlock();

                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true);

                    if (!$('form.form.form-cart').length){
                        $('.cart-summary').css('display','none');
                    }

                    self._addMessage(resp.messages, 'success');

                    $(document).trigger("afterAjaxProductsLoaded");
                    $(document).trigger("afterAjaxLazyLoad");

                    self._unbind();

                } else {
                    self._refreshCartAfterError(resp.messages);
                }
            });
            return false;
        },

        _updateCart: function(){
            var self = this;
            var form = $('form.form.form-cart');
            var _url = form.attr('action').replace('checkout','market');

            if (!form.children("input[name='form_key']").length){
                $("input[name='form_key']").clone().appendTo(form);
            }

            $.ajax({
                url: _url,
                data: form.serialize(),
                method: "post",
                dataType: "json",
                beforeSend: function (){
                    self._showLoader();
                    self._clearMessages();
                },
                success: function (res) {},
                error: function () {
                    console.log('error');
                }
            }).done(function(resp){
                if (resp.success == true) {
                    var newContent = $(resp.content);

                    $('[data-block="minicart"]').trigger('contentLoading');
                    $('form.form.form-cart',$('.cart-container')).replaceWith(newContent.html());

                    //cambio el data_id de customerData como para que se fuerze el update.
                    customerData.get('cart')()['data_id'] = Number(customerData.get('cart')()['data_id'] + 1);
                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                    self._controlCheckoutMethodsBlock();

                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true);

                    self._addMessage(resp.messages, 'success');

                    $(document).trigger("afterAjaxProductsLoaded");
                    $(document).trigger("afterAjaxLazyLoad");

                    self._unbind();
                } else {
                    self._refreshCartAfterError(resp.messages);
                }
            });
        },

        _controlCheckoutMethodsBlock : function(){
            var checkoutMethodsBlock = $('ul.checkout.methods.items.checkout-methods-items');
            if (checkoutConfig.quoteData.has_erros == undefined){
                if (checkoutMethodsBlock.hasClass('hidden')){
                    checkoutMethodsBlock.removeClass('hidden');
                }
            } else {
                checkoutMethodsBlock.addClass('hidden');
            }
        },

        _refreshCartAfterError : function(error){
            var self = this;
            var _url = urlManager.build('market/cart/refreshCart');

            $.ajax({
                url: _url,
                method: "post",
                dataType: "json",
                beforeSend: function (){
                    self._clearMessages();
                },
                success: function (res) {},
                error: function () {
                    console.log('error');
                }
            }).done(function(resp){
                self._hideLoader();
                if (resp.success == true) {
                    var newContent = $(resp.content);

                    $('form.form.form-cart',$('.cart-container')).replaceWith(newContent.html());

                    self._addMessage(error, 'error');

                    $(document).trigger("afterAjaxProductsLoaded");
                    $(document).trigger("afterAjaxLazyLoad");

                    self._bind();
                }
            });
        },

        _addToWishList : function(event){
            event.preventDefault();
            var self = this;
            var item = $(event.target);
            var _data = JSON.parse(item.attr('data-post-url'));
            var _url = _data.action.replace('wishlist/index','market/wishlist');

            _data.data.form_key = $("input[name='form_key']").val();

            $.ajax({
                method: "post",
                url: _url,
                data: _data.data,
                dataType: "json",
                beforeSend: function (){
                    self._showLoader();
                    self._clearMessages();
                },
                success: function (resp) {}
            }).done(function(resp){
                self._hideLoader();

                if (resp.success == true) {

                    var wishlistLink = $('li.link.wishlist a');
                    var wishlistSpan = $('li.link.wishlist a span.counter.qty')
                    if (!wishlistSpan.length){
                        wishlistSpan = $('<span>');
                        wishlistSpan.addClass('counter').addClass('qty').appendTo(wishlistLink);
                    }
                    wishlistSpan.text(resp.wishlistCount);

                    self._addMessage(resp.messages, 'success');


                } else {
                    self._addMessage(resp.messages, 'error');
                }
            });
            return false;
        }

    });
    return $.custom.cart;
});