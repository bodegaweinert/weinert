/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/totals-service',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/cache-key-generator',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/cache',
        'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger'
    ],
    function (
        $,
        ko,
        Component,
        checkoutData,
        selectShippingMethodAction,
        shippingService,
        quote,
        setShippingInformationAction,
        paymentService,
        paymentMethodConverter,
        paymentMethodsService,
        totalsService,
        cacheKeyGenerator,
        cacheStorage,
        completenessLogger
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ids_Andreani/shipping-method'
            },
            isProcessing: false,
            rates: shippingService.getShippingRates(),
            isShown: ko.computed(function () {
                return !quote.isQuoteVirtual();
            }),
            isLoading: shippingService.isLoading,
            isSelected: ko.computed(function () {
                    return quote.shippingMethod() ?
                        quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                        : null;
                }
            ),
            errorValidationMessage: ko.observable(''),

            localidadesDisponibles: ko.observableArray([]),
            localidadSeleccionada:ko.observable(''),
            provinciasDisponibles: ko.observableArray(
                window.andreaniConfig.provincias
            ),
            provinciaSeleccionada:ko.observable(''),
            sucursalesAndreaniDisponibles: ko.observableArray([]),
            sucursalAndreaniSeleccionada: ko.observable(''),

            baseUrl: window.andreaniConfig.baseUrl,

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                quote.shippingMethod.subscribe(function () {
                    this.errorValidationMessage('');
                }, this);
                completenessLogger.bindField('shippingMethod', quote.shippingMethod);

                return this;
            },

            /**
             * Select shipping method
             *
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                var self = this;
                this.isProcessing = true;
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(
                    shippingMethod.carrier_code + '_' + shippingMethod.method_code
                );

                /** AGREGADO ANDREANI **/

                if(shippingMethod.carrier_code == 'andreanisucursal' && shippingMethod.method_code == 'sucursal')
                {
                    $('.retiro-sucursal-row').show();

                    // $('#andreanisucursal-provincia').val('');
                    $('#andreanisucursal-localidad').val('').hide();
                    $('#andreanisucursal-sucursal').val('');
                }
                else
                {
                    $('.retiro-sucursal-row').hide();
                }
                /** /AGREGADO **/

                if (paymentMethodsService) paymentMethodsService.isLoading(true);
                totalsService.isLoading(true);
                setShippingInformationAction().done(
                    function (response) {
                        var methods = paymentMethodConverter(response.payment_methods),
                            cacheKey = cacheKeyGenerator.generateCacheKey({
                                shippingAddress: quote.shippingAddress(),
                                billingAddress: quote.billingAddress(),
                                totals: quote.totals()
                            });

                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methods);
                        cacheStorage.set(
                            cacheKey,
                            {'payment_methods': methods, 'totals': response.totals}
                        );
                    }
                ).always(
                    function () {
                        if (paymentMethodsService) paymentMethodsService.isLoading(false);
                        totalsService.isLoading(false);
                        self.isProcessing = false;
                        $(document).trigger('forceReloadMP');
                    }
                );

                return true;
            },

            /**
             * @inheritdoc
             */
            validate: function () {
                if (!quote.shippingMethod() && !quote.isQuoteVirtual()) {
                    this.errorValidationMessage('Please specify a shipping method.');
                    this.source.set('params.invalid', true);
                }

                if (quote.shippingMethod().carrier_code == 'andreanisucursal' && $('#andreanisucursal-sucursal').val() == '') {
                    this.errorValidationMessage('Por favor seleccioná una sucursal de Andreani.');
                    this.source.set('params.invalid', true);
                }
            },
            getLocalidades: function ()
            {
                var provinciaSeleccionada = this.provinciaSeleccionada();
                $('.localidad-sin-sucursales').hide();

                if(provinciaSeleccionada)
                {
                    this.localidadesDisponibles([]);
                    this.sucursalesAndreaniDisponibles([]);
                    $('#andreanisucursal-localidad').hide();
                    $('#andreanisucursal-sucursal').hide();
                    $('.retiro-sucursal-row').addClass('andreani-loader');

                    $.ajax(this.baseUrl + 'andreani/localidad/index',
                        {
                            type    : 'post',
                            context : this,
                            data    :
                                {
                                    provincia_id: provinciaSeleccionada
                                },
                            success : function (response)
                            {
                                for(var i = 0; i < response.length; i++)
                                {
                                    this.localidadesDisponibles.push(response[i]);
                                }
                                $('#andreanisucursal-localidad').show();
                                $('.retiro-sucursal-row').removeClass('andreani-loader');
                            },
                            error   : function (e, status)
                            {
                                alert("Se produjo un error, por favor intentelo nuevamente");
                                $('.retiro-sucursal-row').removeClass('andreani-loader');
                            }
                        });
                }
            },
            getSucursales: function ()
            {
                $('.localidad-sin-sucursales').hide();
                // $('#andreanisucursal-sucursal').val('').hide();


                var provinciaSeleccionada = $('select[name="region_id"] option:selected').text();
                var localidadSeleccionada = $('input[name="city"]').val();
                var codigoPostal = $('input[name="postcode"]').val();

                if ((codigoPostal == undefined || codigoPostal == '') && window.checkoutConfig.isCustomerLoggedIn){
                    codigoPostal = window.checkoutConfig.customerData.addresses[0].postcode;
                }
                codigoPostal = codigoPostal.match(/\d+/);

                this.sucursalesAndreaniDisponibles([]);
                $('.retiro-sucursal-row').addClass('andreani-loader');

                $.ajax(this.baseUrl + 'andreani/sucursal/index',
                    {
                        type    : 'post',
                        context : this,
                        data    :
                            {
                                provincia: provinciaSeleccionada,
                                localidad: localidadSeleccionada,
                                codigoPostal: codigoPostal
                            },
                        success : function (response)
                        {
                            if(response.length)
                            {
                                $('#andreanisucursal-sucursal').show();

                                for(var i = 0; i < response.length; i++)
                                {
                                    this.sucursalesAndreaniDisponibles.push(response[i]);
                                }
                            }
                            else
                            {
                                $('.localidad-sin-sucursales').show();
                                $('#andreanisucursal-sucursal').hide();
                            }
                            $('.retiro-sucursal-row').removeClass('andreani-loader');
                        },
                        error   : function (e, status)
                        {
                            alert("Se produjo un error, por favor intentelo nuevamente");
                            $('.retiro-sucursal-row').removeClass('andreani-loader');
                        }
                    });

            },
            cotizacionAndreaniSucursal: function ()
            {
                var provinciaOrigen = $('select[name="region_id"] option:selected').val();

                if(this.sucursalAndreaniSeleccionada())
                {
                    $('.retiro-sucursal-row').addClass('andreani-loader');

                    $.ajax(this.baseUrl + 'andreani/webservice/cotizar',
                        {
                            type    : 'post',
                            context : this,
                            data    :
                                {
                                    tipo        : 'sucursal',
                                    /**
                                     * Posiblemente el quote id este de más aca... ver bien
                                     */
                                    quoteId     : quote.getQuoteId(),
                                    sucursalId  : this.sucursalAndreaniSeleccionada(),
                                    /**
                                     * Temporal: esto se debe cargar directamente cuando se aplica la sucursal en el controller
                                     */
                                    sucursalTxt : $('#andreanisucursal-sucursal option:selected').text(),
                                    provinciaOrigen : provinciaOrigen
                                },
                            success : function (response)
                            {
                                if(typeof response.cotizacion == 'undefined')
                                {
                                    $('.retiro-sucursal-row').removeClass('andreani-loader');
                                    alert('No se encontraron cotizaciones para el envío a esta sucursal. Por favor intentelo nuevamente seleccionando otra.')
                                }
                                else
                                {
                                    //Ver la mejor manera de ponerle el precio
                                    $('div.andreanisucursal-price span.price').html(response.cotizacion);
                                    $('.retiro-sucursal-row').removeClass('andreani-loader');

                                    $.each(this.rates(), function (index, shippingMethod)
                                    {
                                        //console.log(shippingMethod.method_code);
                                        if(shippingMethod.method_code == 'sucursal')
                                        {
                                            shippingMethod.method_title = response.method_title;
                                            shippingMethod.amount = response.cotizacion;

                                            selectShippingMethodAction(shippingMethod);
                                            checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                                        }
                                    });
                                    $(document).trigger('forceReloadMP');
                                }
                            },
                            error   : function (e, status)
                            {
                                alert("Se produjo un error, por favor intentelo nuevamente");
                                $('.retiro-sucursal-row').removeClass('andreani-loader');
                            }
                        });
                }

                return false;
            }
        });
    }
);
