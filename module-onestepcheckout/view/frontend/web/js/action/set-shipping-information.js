/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'underscore',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/select-billing-address',
        'Aheadworks_OneStepCheckout/js/model/shipping-information/service-busy-flag',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag'
    ],
    function (
        _,
        $,
        quote,
        resourceUrlManager,
        storage,
        errorProcessor,
        selectBillingAddressAction,
        serviceBusyFlag,
        sameAsShippingFlag
    ) {
        'use strict';
        return function () {
            var payload;

            if (!quote.billingAddress() || !quote.isQuoteVirtual() && sameAsShippingFlag.sameAsShipping()) {
                selectBillingAddressAction(quote.shippingAddress());
            }

            payload = {
                addressInformation: {
                    shipping_address: _.extend(
                        {},
                        quote.shippingAddress(),
                        {'same_as_billing': !quote.isQuoteVirtual() && sameAsShippingFlag.sameAsShipping() ? 1 : 0}
                    ),
                    billing_address: quote.billingAddress(),
                    shipping_method_code: quote.shippingMethod().method_code,
                    shipping_carrier_code: quote.shippingMethod().carrier_code,
                    extension_attributes: {
                        // Send data to extension variables through payload.
                        pickup_store: $("#pickup-store").val(),
                        oca_store: $("#ocasucursales-store").val()
                    }
                }
            };

            /* adding custom address attributes to shipping_address.extension_attributes */
            var additionalData = {extension_attributes:{}};

            if (payload.addressInformation.shipping_address.customAttributes !== undefined){
                if (payload.addressInformation.shipping_address.customAttributes.dni !== undefined){
                    if (payload.addressInformation.shipping_address.customAttributes.dni.value != undefined){
                        additionalData.extension_attributes.dni = payload.addressInformation.shipping_address.customAttributes.dni.value;
                    } else {
                        additionalData.extension_attributes.dni = payload.addressInformation.shipping_address.customAttributes.dni;
                    }
                }

                if (payload.addressInformation.shipping_address.customAttributes.dob !== undefined){
                    if (payload.addressInformation.shipping_address.customAttributes.dob.value != undefined){
                        additionalData.extension_attributes.dob = payload.addressInformation.shipping_address.customAttributes.dob.value;
                    } else {
                        additionalData.extension_attributes.dob = payload.addressInformation.shipping_address.customAttributes.dob;
                    }
                }

                if (payload.addressInformation.shipping_address.customAttributes.street_number !== undefined){
                    if (payload.addressInformation.shipping_address.customAttributes.street_number.value != undefined){
                        additionalData.extension_attributes.street_number = payload.addressInformation.shipping_address.customAttributes.street_number.value;
                    } else {
                        additionalData.extension_attributes.street_number = payload.addressInformation.shipping_address.customAttributes.street_number;
                    }
                }
                if (payload.addressInformation.shipping_address.customAttributes.street_floor !== undefined){
                    if (payload.addressInformation.shipping_address.customAttributes.street_floor.value != undefined){
                        additionalData.extension_attributes.street_floor = payload.addressInformation.shipping_address.customAttributes.street_floor.value;
                    } else {
                        additionalData.extension_attributes.street_floor = payload.addressInformation.shipping_address.customAttributes.street_floor;
                    }
                }
                if (payload.addressInformation.shipping_address.customAttributes.street_apartment !== undefined){
                    if (payload.addressInformation.shipping_address.customAttributes.street_apartment.value != undefined){
                        additionalData.extension_attributes.street_apartment = payload.addressInformation.shipping_address.customAttributes.street_apartment.value;
                    } else {
                        additionalData.extension_attributes.street_apartment = payload.addressInformation.shipping_address.customAttributes.street_apartment;
                    }
                }

                //business information
                if (payload.addressInformation.shipping_address.customAttributes.require_invoice !== undefined){
                    // if (payload.addressInformation.shipping_address.customAttributes.require_invoice.value != undefined){
                    //     additionalData.extension_attributes.require_invoice = payload.addressInformation.shipping_address.customAttributes.require_invoice.value;
                    // } else {
                    additionalData.extension_attributes.require_invoice = $('input[name=custom_attributes\\[require_invoice\\]]').prop('checked');
                    // }
                }

                if (payload.addressInformation.shipping_address.customAttributes.cuit !== undefined){
                    // if (payload.addressInformation.shipping_address.customAttributes.cuit.value != undefined){
                    //     additionalData.extension_attributes.cuit = payload.addressInformation.shipping_address.customAttributes.cuit.value;
                    // } else {
                    additionalData.extension_attributes.cuit = $('input[name=custom_attributes\\[cuit\\]]').val();
                    // }
                }

                if (payload.addressInformation.shipping_address.customAttributes.business_name !== undefined){
                    // if (payload.addressInformation.shipping_address.customAttributes.business_name.value != undefined){
                    //     additionalData.extension_attributes.business_name = payload.addressInformation.shipping_address.customAttributes.business_name.value;
                    // } else {
                    additionalData.extension_attributes.business_name = $('input[name=custom_attributes\\[business_name\\]]').val();
                    // }
                }

                if ( $('select[name=custom_attributes\\[convenio\\]]').length > 0){
                    additionalData.extension_attributes.convenio = $('select[name=custom_attributes\\[convenio\\]]').val();
                }

                //if (payload.addressInformation.shipping_address.customAttributes.convenio !== undefined){
                    // if (payload.addressInformation.shipping_address.customAttributes.convenio.value != undefined){
                    //     additionalData.extension_attributes.convenio = payload.addressInformation.shipping_address.customAttributes.convenio.value;
                    // } else {
                    //additionalData.extension_attributes.convenio = $('select[name=custom_attributes\\[convenio\\]]').val();
                    // }
                //}


            }

            payload.addressInformation.shipping_address = _.extend(payload.addressInformation.shipping_address, additionalData);

            /* adding custom address attributes to billing_address.extension_attributes */
            // payload.addressInformation.billing_address = _.extend(payload.addressInformation.billing_address, additionalData);
            /*******************************************************************************************************************/

            serviceBusyFlag(true);

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function () {
                    serviceBusyFlag(false);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
        }
    }
);
