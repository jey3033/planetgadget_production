define([
    'ko',
    'Kemana_ShippingInsurance/js/view/checkout/summary/insuranceFee',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/cart/estimate-service'
], function (ko, Component, quote, priceUtils, totals) {
    'use strict';

    var show_hide_insurance_fee = window.checkoutConfig.show_hide_insurance_fee;
    var insurance_fee_title = window.checkoutConfig.insurance_fee_title;
    var insurance_fee_amount = window.checkoutConfig.insurance_fee_amount;

    return Component.extend({
        totals: quote.getTotals(),
        canVisibleInsuranceFeeBlock: show_hide_insurance_fee,
        getFormattedPrice: ko.observable(priceUtils.formatPrice(insurance_fee_amount, quote.getPriceFormat())),
        getInsuranceFeeTitle:ko.observable(insurance_fee_title),
        isDisplayed: function () {
            return this.getValue() != 0;
        },
        getValue: function() {
            var price = 0;
            if (this.totals() && totals.getSegment('insurance_fee')) {
                price = totals.getSegment('insurance_fee').value;
            }
            return price;
        }
    });
});