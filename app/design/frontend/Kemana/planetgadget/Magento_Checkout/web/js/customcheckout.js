require([
        "jquery",
        "matchMedia",
    ], function ($,mediaCheck) {
        "use strict";

    mediaCheck({
        media: '(max-width: 767px)',
        // Switch to Desktop Version
        entry: function () {
            $('.summary-totals').hide();
            $('.opc-block-shipping-information').hide();

            $(document).on("click",".opc-block-summary .items-in-cart > .title",function(){
                if($(window).width() < 767)
                {
                    if($(this).parent().hasClass('active')){
                        $('.summary-totals').show()
                        $('.opc-block-shipping-information').show()
                    }else{  
                        $('.summary-totals').hide()
                        $('.opc-block-shipping-information').hide()
                    }
                }
            })
        },
        // Switch to Mobile Version
        exit: function () {
            $('.summary-totals').show()
        }
    });
});