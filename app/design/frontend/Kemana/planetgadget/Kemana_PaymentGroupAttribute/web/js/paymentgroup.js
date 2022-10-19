define([
    'jquery',
    'Magento_Checkout/js/action/select-payment-method'
], function ($,selectPaymentMethod) {
    'use strict';

    $.widget('kemana.paymentgroup',{
        options: {},

        _create:function(){
            let self = this;
            self._bindEvents();
            self._setView();
        },

        _bindEvents:function(){
            let self = this;
            self.element.find('.psg-tab-item .title input').change(function(){
                self.element.find('.psg-tab-item .title input').prop("checked", false);
                self.element.find('.psg-tab-item .content').hide();
                $(this).prop("checked", true);
                $(this).parent('.title').siblings().show();
                selectPaymentMethod();

            });
            
            $(document).on("click",".without-group .radios",function() {
                $('.psg-tab-item.group .title input').prop("checked", false);
                $('.psg-tab-item .content').hide();
                $(this).prop("checked", false);
            });
        },

        _setView:function () {
            let self = this;

            if(self.element.find('.psg-tab-item')){
                self.element.find('.psg-tab-item .content').hide();
            }
        }
    });

    return $.kemana.paymentgroup;

});
