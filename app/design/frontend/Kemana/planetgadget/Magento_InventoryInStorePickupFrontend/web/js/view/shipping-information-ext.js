/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/model/quote',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service'
], function (quote,pickupLocationsService) {
    'use strict';

    var storePickupShippingInformation = {
        defaults: {
            template: 'Magento_InventoryInStorePickupFrontend/shipping-information',
            selectedLocation: pickupLocationsService.selectedLocation
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
                locationName = quote.shippingAddress().firstname + ' ' + quote.shippingAddress().lastname;
            }

            return locationName;
        },
        getShippingMethodPrice: function () {
            var shippingMethod = quote.shippingMethod(),
                shippingPrice = '';

            if (typeof quote.shippingMethod().amount != 'undefined' && !this.isStorePickup()) {
                shippingPrice = '-' + ' Rp ' + quote.shippingMethod().amount;
            }else{
                shippingPrice = 'Rp 0';
            }

            return shippingPrice;
        },
        getShippingMethodAddress: function () {
            var shippingMethod = quote.shippingMethod(),
                locationName = '';

            if (!this.isStorePickup()) {

                return '-';
            }


            if (typeof quote.shippingAddress().firstname !== undefined) {
                locationName = quote.shippingAddress().street;
            }

            return locationName;
        },
        getShippingMethodTelephone: function () {
            var shippingMethod = quote.shippingMethod(),
                locationName = '';

            if (!this.isStorePickup()) {

                return '-';
            }


            if (typeof quote.shippingAddress().firstname !== undefined) {
                locationName = quote.shippingAddress().telephone;
            }

            return locationName;
        },

        getShippingMethodFrontendDescription: function () {
            var shippingMethod = quote.shippingMethod(),
                locationName = '';

            if (!this.isStorePickup()) {

                return '-';
            }


            if (typeof quote.shippingAddress().frontend_description !== undefined) {
                locationName = quote.shippingAddress().frontend_description;
            }

            return locationName;
        },

        getShippingMethodDate: function() {
            return window.checkoutConfig.quoteData.delivery_date;
        },

        getShippingMethodTime: function() {
            return window.checkoutConfig.quoteData.delivery_time;
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
