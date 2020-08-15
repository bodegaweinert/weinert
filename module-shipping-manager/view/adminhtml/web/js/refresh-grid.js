require(
    [
        'jquery',
        'mage/translate',
        'uiRegistry'
    ],
    function ($,t,registry) {
        $(document).bind('applyMassAction',function () {
            setTimeout(function(){
                var params = [];
                var target = registry.get('shippingmanager_sales_order_grid.shippingmanager_sales_order_grid_data_source');
                if (target && typeof target === 'object') {
                    target.set('params.t ', Date.now());
                }
            }, 5000);
        });
    }
);