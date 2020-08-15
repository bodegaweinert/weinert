require(
    [
        'jquery'
    ],
    function ($) {
        var coupon = '#newsletter_coupon_coupon_code';
        var coupon_fields = [
            '#newsletter_coupon_code_length',
            '#newsletter_coupon_code_format',
            '#newsletter_coupon_code_prefix',
            '#newsletter_coupon_code_suffix',
            '#newsletter_coupon_dash_every_x_characters'
        ];

        $(document).ready(function(){
            if ($(coupon).length){
                $(coupon).change(function(){
                    var elements = coupon_fields;

                    if ($(coupon).val() == 0){
                        for (var i =0; i< elements.length; i++){
                            var element = $(elements[i]);
                            element.attr('disabled','disabled');
                        }
                    } else {
                        for (var j =0; j< elements.length; j++){
                             var field = $(elements[j]);
                            field.removeAttr('disabled');
                        }
                    }
                });
                $(coupon).change();
            }
        });
    }
);