define([
    'jquery'
], function ($) {
    'use strict';

    return function (validator) {
        validator.addRule(
            'validate-mayor',
            function (value) {
                // obtain values for validation
                var validator = this;


                var fecha = value.substr(6,4) + "/" + value.substr(3,2) + "/" + value.substr(0,2);

                var hoy = new Date();
                var cumpleanos = new Date(fecha);
                var edad = hoy.getFullYear() - cumpleanos.getFullYear();
                var m = hoy.getMonth() - cumpleanos.getMonth();

                if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                    edad--;
                }

                if(edad < 18){
                    return false;
                }

                return true;
            },
            $.mage.__('Prohibida la venta a menores de 18 años.')
        );
        return validator;
    };
});