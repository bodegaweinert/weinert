define([
    //'Magento_Ui/js/grid/massactions'
    'jquery'
], function (/*massactions*/$) {
    'use strict';

    return function (target) {
        return target.extend({
            applyAction: function (actionIndex) {
                // call parent
                this._super(actionIndex);
                $(document).trigger('applyMassAction');
            }
        });
    };
});