define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function ($,_,uiRegistry, select, modal) {
    'use strict';

    return select.extend({
        initialize: function () {
            //var value = uiRegistry.get('index = mposition').value();
            this._super();
            var valueSelect = this.value();
            var tid = setInterval(function(){
                //console.log(valueSelect);
                if (valueSelect ==0){
                    $('div[data-index="notification_email_receiver"]').show();
                }
                else
                {
                    $('div[data-index="notification_email_receiver"]').hide();
                }
                return this;
            },1000);
            setTimeout(function(){
                 clearInterval(tid); //clear above interval after 5 seconds
            },3000);
        },
        /**
         * On value change handler.
         *
         * @param {int} value
         */
        onUpdate: function (value) {
            if (value ==0){
                $('div[data-index="notification_email_receiver"]').show();
            }
            else
            {
                $('div[data-index="notification_email_receiver"]').hide();
            }
            return this._super();
        },
    });
});