/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    var storePickupShippingInformation = {
        defaults: {
            template: 'Magento_InventoryInStorePickupFrontend/shipping-information'
        },

        /**
         * Get shipping method title based on delivery method.
         *
         * @return {String}
         */
        getShippingMethodTitle: function () {
            var shippingMethod = quote.shippingMethod(),
                locationName = '';

            if (!this.isStorePickup()) {

                return this._super();
            }


            if (quote.shippingAddress().firstname !== undefined) {
                locationName = quote.shippingAddress().firstname + ' ' + quote.shippingAddress().lastname+ ' ' + quote.shippingAddress().street+ ' ' + quote.shippingAddress().telephone;
            }

            return locationName;
        },

        /**
         * Get is store pickup delivery method selected.
         *
         * @returns {Boolean}
         */
        isStorePickup: function () {
            var shippingMethod = quote.shippingMethod(),
                isStorePickup = false;

            if (shippingMethod !== null) {
                isStorePickup = shippingMethod['carrier_code'] === 'instore' &&
                    shippingMethod['method_code'] === 'pickup';
            }

            return isStorePickup;
        }
    };

    return function (shippingInformation) {
        return shippingInformation.extend(storePickupShippingInformation);
    };
});
