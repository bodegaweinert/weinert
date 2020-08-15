/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag'
    ],
    function ($, wrapper, quote, sameAsShippingFlag) {
        'use strict';

        return function (selectBillingAddressAction) {
            return wrapper.wrap(selectBillingAddressAction, function (originalAction, billingAddress) {
                var address = null,
                    sameAsBilling = !quote.isQuoteVirtual() && sameAsShippingFlag.sameAsShipping();

                if (quote.shippingAddress() && billingAddress.getCacheKey() == quote.shippingAddress().getCacheKey()) {
                    address = $.extend({}, billingAddress);
                    address.saveInAddressBook = sameAsBilling && quote.shippingAddress().saveInAddressBook ? 1 : 0;
                } else {
                    address = billingAddress;
                }

                /* adding custom address attributes to address.extension_attributes */
                if (address.customAttributes !== undefined){
                    var additionalData = {extension_attributes:{}};
                    if (address.customAttributes.dni !== undefined){
                        //additionalData.extension_attributes.dni = address.customAttributes.dni;
                        if (address.customAttributes.dni.value != undefined){
                            additionalData.extension_attributes.dni = address.customAttributes.dni.value;
                        } else {
                            additionalData.extension_attributes.dni = address.customAttributes.dni;
                        }
                    }

                    if (address.customAttributes.dob !== undefined){
                        //additionalData.extension_attributes.dni = address.customAttributes.dni;
                        if (address.customAttributes.dob.value != undefined){
                            additionalData.extension_attributes.dob = address.customAttributes.dob.value;
                        } else {
                            additionalData.extension_attributes.dob = address.customAttributes.dob;
                        }
                    }

                    if (address.customAttributes.street_number !== undefined){
                        if (address.customAttributes.street_number.value != undefined){
                            additionalData.extension_attributes.street_number = address.customAttributes.street_number.value;
                        } else {
                            additionalData.extension_attributes.street_number = address.customAttributes.street_number;
                        }
                    }
                    if (address.customAttributes.street_floor !== undefined){
                        if (address.customAttributes.street_floor.value != undefined){
                            additionalData.extension_attributes.street_floor = address.customAttributes.street_floor.value;
                        } else {
                            additionalData.extension_attributes.street_floor = address.customAttributes.street_floor;
                        }
                    }
                    if (address.customAttributes.street_apartment !== undefined){
                        if (address.customAttributes.street_apartment.value != undefined){
                            additionalData.extension_attributes.street_apartment = address.customAttributes.street_apartment.value;
                        } else {
                            additionalData.extension_attributes.street_apartment = address.customAttributes.street_apartment;
                        }
                    }

                    //business information
                    if (address.customAttributes.require_invoice !== undefined){
                        if (address.customAttributes.require_invoice.value != undefined){
                            additionalData.extension_attributes.require_invoice = address.customAttributes.require_invoice.value;
                        } else {
                            additionalData.extension_attributes.require_invoice = address.customAttributes.require_invoice;
                        }
                    }

                    if (address.customAttributes.cuit !== undefined){
                        if (address.customAttributes.cuit.value != undefined){
                            additionalData.extension_attributes.cuit = address.customAttributes.cuit.value;
                        } else {
                            additionalData.extension_attributes.cuit = address.customAttributes.cuit;
                        }
                    }

                    if (address.customAttributes.business_name !== undefined){
                        if (address.customAttributes.business_name.value != undefined){
                            additionalData.extension_attributes.business_name = address.customAttributes.business_name.value;
                        } else {
                            additionalData.extension_attributes.business_name = address.customAttributes.business_name;
                        }
                    }

                    if (address.customAttributes.convenio !== undefined){
                        if (address.customAttributes.convenio.value != undefined){
                            additionalData.extension_attributes.convenio = address.customAttributes.convenio.value;
                        } else {
                            additionalData.extension_attributes.convenio = address.customAttributes.convenio;
                        }
                    }

                    address = $.extend(address, additionalData);
                }

                quote.billingAddress(address);
            });
        };
    }
);
