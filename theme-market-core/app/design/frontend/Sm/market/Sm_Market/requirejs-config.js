var config = {
    map: {
        '*': {
            jquerypopper: "Sm_Market/js/bootstrap/popper",
            jquerybootstrap: "Sm_Market/js/bootstrap/bootstrap.min",
            owlcarousel: "Sm_Market/js/owl.carousel",
            jqueryfancyboxpack: "Sm_Market/js/jquery.fancybox.pack",
            jqueryunveil: "Sm_Market/js/jquery.unveil",
            yttheme: "Sm_Market/js/yttheme",
            customAjaxCart : "Sm_Market/js/customAjaxCart.js",
            customCart : "Sm_Market/js/customCart.js",
            customReview : "Sm_Market/js/customReview.js",
            sidebar : 'Sm_Market/js/sidebar.js',
            customerLogin : 'Sm_Market/js/customerLogin.js',
            customContact : "Sm_Market/js/customContact.js",
            systemMessage : "Sm_Market/js/systemMessage.js"
        }
    },
    shim: {
        'jquerypopper': {
            'deps': ['jquery'],
            'exports': 'Popper'
        },
        'jquerybootstrap': {
            'deps': ['jquery', 'jquerypopper']
        }
    }
};