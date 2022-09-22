define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/totals',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
], function (
    $,
    _,
    Component,
    ko,
    quote,
    setShippingInformationAction,
    stepNavigator,
    modal,
    checkoutDataResolver,
    checkoutData,
    registry,
    $t,
    totals,
    confirmation,
    cartCache,
    defaultTotal,
) {
    'use strict';
    return function (Component) {
        return Component.extend({

            defaults: {
                shippingMethodTempTemplate: 'Kemana_ShippingInsurance/checkout/shipping/insurance'
            },
            isChecked: ko.observable(false),
            isVisibleTemp: ko.observable(false),

            initialize: function () {
                this._super();
                var self = this;
                let totalsValue;    
                if (totals.totals()) {  
                    totalsValue = totals.getSegment('grand_total').value;   
                }   

                if (totals.getSegment('insurance_fee')) {
                    var price = totals.getSegment('insurance_fee').value;
                    if(price){
                        this.isChecked(true)
                    }
                }

                if (window.checkoutConfig.insurance && quote.shippingMethod() && quote.shippingMethod()['carrier_code']) {
                    self.isVisibleTemp(true)
                }

                quote.shippingMethod.subscribe(function(value) {
                if (value) {
                    if (value.carrier_code == 'jne') {
                            self.isVisibleTemp(true)
                            self.isChecked(true) 
                        }else{
                            self.isVisibleTemp(false)
                            self.isChecked(false); 
                        };
                    }else{
                        self.isVisibleTemp(false)
                        self.isChecked(false); 
                    }
                });
            },

            /**
             * Set shipping information handler
             */
            setShippingInformation: function (redirect = true) {
                if (this.validateShippingInformation()) {
                    quote.billingAddress(null);
                    checkoutDataResolver.resolveBillingAddress();
                    registry.async('checkoutProvider')(function (checkoutProvider) {
                        var shippingAddressData = checkoutData.getShippingAddressFromData();
                        
                        if (shippingAddressData) {
                            checkoutProvider.set(
                                'shippingAddress',
                                $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                            );
                        }
                    });
                    setShippingInformationAction().done(
                        function () {
                            if(redirect) {
                                stepNavigator.next();
                            }
                        }
                    );
                }
                cartCache.set('totals',null);
                defaultTotal.estimateTotals();
            },

            getClassInput: ko.computed(function () {
                return 'checkbox';
            }),

            updateTotalsInSummary: function() {
                /**
                 * Update cart totals in summary
                 */
                var redirect = false;
                this.setShippingInformation(redirect);
                return true;
            },

            /**
             * Custom temp for all shipping method
             */
            eventClick: function (obj, event) {
                /*return validate;*/
                if (!this.validateShippingInformation()) {
                    return false;
                }
                var currentThis = this;
                /* popup code */
                var checkbox = event.target;
                var subtotal = totals.totals()['subtotal'];
                if(!$(checkbox).is(':checked')) {
                    confirmation({
                        title: $.mage.__('Are you sure?'),
                        content: $.mage.__('JIKA ANDA TIDAK MENGGUNAKAN ASURANSI, APABILA TERJADI KEHILANGAN, KAMI TIDAK BERTANGGUNG JAWAB.'),
                        modalClass: 'insurance-confirm confirm',
                        actions: {
                            confirm: function() {
                                $(checkbox).prop('checked', false).change();
                                currentThis.updateTotalsInSummary();
                                return true;
                            },
                            cancel: function() {
                                $(checkbox).prop('checked', true).change();
                                return false;
                            }
                        },
                        buttons: [{
                            text: $.mage.__('BATAL'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $.mage.__('SETUJU'),
                            class: 'action-primary action-accept',
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                }
                if($(checkbox).is(':checked')){
                    currentThis.updateTotalsInSummary();
                    return true;
                } else {
                    return false;
                }
            } 
        });
    }
});
