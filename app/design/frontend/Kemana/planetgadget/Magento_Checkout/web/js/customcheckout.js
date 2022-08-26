require([
        "jquery",
        "matchMedia",
    ], function ($,mediaCheck) {
        "use strict";

    mediaCheck({
        media: '(max-width: 767px)',
        // Switch to Desktop Version
        entry: function () {
            $(document).on("click",".opc-block-summary .items-in-cart > .title",function(){
                if($(window).width() < 767)
                {
                    if($(this).parent().hasClass('active')){
                        $('.summary-totals').show()
                    }else{  
                        $('.summary-totals').hide()
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