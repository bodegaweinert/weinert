/**
 * @description Sobreescritura del js y template html que manejan la logica y renderizacion de
 *              los metodos de envio en el checkout.
 *
 *
 * @file Magento_Checkout/js/view/shipping-address/address-renderer/default Se sobreescribe para que al seleccionar una direccion de
 * envio, los radiobutton de seleccion se reseteen.
 *
 * @file Magento_Checkout/js/view/shipping Se sobreescribe para añadir la logica de seleccion de sucursales andreani, y
 * mejorar el comportamiento por defecto de magento.
 *
 * @file Magento_Checkout/template/shipping.html Se sobreescribe para que al seleccionar una direccion de
 * envio, los radiobutton de seleccion se reseteen.
 *
 * @type {{map: {*: {}}}}
 */
var config = {
    map: {
        '*': {
            'Magento_Checkout/js/model/shipping-save-processor/default':
                'Ids_Andreani/js/model/shipping-save-processor/default',
            'Magento_Checkout/template/cart/shipping-rates':
                'Ids_Andreani/template/cart/shipping-rates',
            'Magento_Checkout/js/view/cart/shipping-rates':
                'Ids_Andreani/js/view/cart/shipping-rates',
            'Magento_Checkout/js/model/checkout-data-resolver':
                'Ids_Andreani/js/model/checkout-data-resolver',
            'Aheadworks_OneStepCheckout/js/view/shipping-method':
                'Ids_Andreani/js/view/shipping-method'

        }
    }
};