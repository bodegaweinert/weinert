define([
    "jquery", "Magento_Customer/js/customer-data"
], function($, customerData) {
    "use strict";
    return function (config, element) {
        var counter = 0;
        var firstname = customerData.get('customer')().firstname;
        if (typeof (firstname) === "undefined") {
            customerData.reload('customer');
        }
        var check = setInterval(function () {
            var firstname = customerData.get('customer')().firstname;
            if (firstname) {
                $(element).text(firstname);
                clearInterval(check);
            }
            counter++;
            if (counter > 10){
                clearInterval(check);
            }
        }, 500);
    };
});