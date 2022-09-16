define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {

            var shippingAddress = quote.shippingAddress();

            if (shippingAddress['extensionAttributes'] === undefined) {
                shippingAddress['extensionAttributes'] = {};
            }

            var is_insurance = 0;
            if ($('#checkout-shipping-method-insurance input:checked').length) {
                is_insurance = 1
            }

            shippingAddress['extensionAttributes']['is_insurance'] = is_insurance;

            return originalAction(messageContainer);
        });
    };
});