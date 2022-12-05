/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service'
], function (
    $,
    _,
    Component,
    registry,
    modal,
    quote,
    customer,
    stepNavigator,
    addressConverter,
    setShippingInformationAction,
    pickupLocationsService
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Magento_InventoryInStorePickupFrontend/store-selector',
            selectedLocationTemplate:
                'Magento_InventoryInStorePickupFrontend/store-selector/selected-location',
            storeSelectorPopupTemplate:
                'Magento_InventoryInStorePickupFrontend/store-selector/popup',
            storeSelectorPopupItemTemplate:
                'Magento_InventoryInStorePickupFrontend/store-selector/popup-item',
            loginFormSelector:
                '#store-selector form[data-role=email-with-possible-login]',
            defaultCountryId: window.checkoutConfig.defaultCountryId,
            delimiter: window.checkoutConfig.storePickupApiSearchTermDelimiter,
            selectedLocation: pickupLocationsService.selectedLocation,
            quoteIsVirtual: quote.isVirtual,
            searchQuery: '',
            nearbyLocations: null,
            isLoading: pickupLocationsService.isLoading,
            popup: null,
            searchDebounceTimeout: 300,
            imports: {
                nearbySearchRadius: '${ $.parentName }:nearbySearchRadius',
                nearbySearchLimit: '${ $.parentName }:nearbySearchLimit'
            },
            gmap: null,
            selectedlocation:null,
        },

        /**
         * Init component
         *
         * @return {exports}
         */
        initialize: function () {
        
            var updateNearbyLocations, country;

            this._super();
            updateNearbyLocations = _.debounce(function (searchQuery) {
                country = quote.shippingAddress() && quote.shippingAddress().countryId ?
                    quote.shippingAddress().countryId : this.defaultCountryId;
                searchQuery = this.getSearchTerm(searchQuery, country);
                this.updateNearbyLocations(searchQuery);
            }, this.searchDebounceTimeout).bind(this);
            this.searchQuery.subscribe(updateNearbyLocations);
            
            return this;
        },

        /**
         * Init component observable variables
         *
         * @return {exports}
         */
        initObservable: function () {
            return this._super().observe(['nearbyLocations', 'searchQuery']);
        },

        /**
         * Set shipping information handler
         */
        setPickupInformation: function () {
            if (this.validatePickupInformation()) {
                setShippingInformationAction().done(function () {
                    stepNavigator.next();
                });
            }
        },

        /**
         * @return {*}
         */
        getPopup: function () {
            if (!this.popup) {
                this.popup = modal(
                    this.popUpList.options,
                    $(this.popUpList.element)
                );
            }

            return this.popup;
        },

        /**
         * Get Search Term from search query and country.
         *
         * @param {String} searchQuery
         * @param {String} country
         * @returns {String}
         */
        getSearchTerm: function (searchQuery, country) {
            return searchQuery ? searchQuery + this.delimiter + country : searchQuery;
        },

        /**
         * @returns void
         */
        openPopup: function () {
            var shippingAddress = quote.shippingAddress(),
                country = shippingAddress.countryId ? shippingAddress.countryId :
                this.defaultCountryId,
                searchTerm = '';

            this.getPopup().openModal();

            if (shippingAddress.city && shippingAddress.postcode) {
                searchTerm = this.getSearchTerm(shippingAddress.postcode, country);
            }

            this.gmap = new google.maps.Map(document.getElementById("store-map"), {
                    center: {lat: -6.176420, lng: 106.826806},
                    zoom: 12
                });

            this.updateNearbyLocations(searchTerm);
        },

        addMarker: function (latitude, longitude) {
            this.gmap = new google.maps.Map(document.getElementById("store-map"), {
                    center: {lat: latitude, lng: longitude},
                    zoom: 20
                });

            const iconBase = "/media/kemana/kslocator/";

            var icon = {
                 url: iconBase + 'PGStore2x.png', // url
                 scaledSize: new google.maps.Size(48, 65), // size
                 origin: new google.maps.Point(0,0)
             };

            var marker = new google.maps.Marker({
                position: {lat: latitude, lng: longitude},
                map: this.gmap,
                animation: google.maps.Animation.DROP,
                icon: icon
            });
        },

        /**
         * @param {Object} location
         * @returns void
         */
        selectPickupLocation: function (location) {
            this.selectedlocation = location;
            console.log(this.selectedlocation)
            if(location.latitude && location.longitude){
                this.addMarker(location.latitude,location.longitude)
            }
            return true;
        },

        submitselectPickupLocation: function () {
            if(this.selectedlocation){
                pickupLocationsService.selectForShipping(this.selectedlocation);
                this.getPopup().closeModal();
            }else{
                return false;
            }
        },

        /**
         * @param {Object} location
         * @returns {*|Boolean}
         */
        isPickupLocationSelected: function (location) {
            var selected = _.isEqual(this.selectedLocation(), location);
            if(selected){
                this.selectedlocation = location;
            }
            return selected;
        },

        /**
         * @param {Object} location
         * @returns {*|Boolean}
         */
        getDistance: function(location) {
            if (navigator.geolocation) {
               var showlocation =  navigator.geolocation.getCurrentPosition(showPosition);
            } else { 
                console.log("Geolocation is not supported by this browser.");
            }
            function showPosition(position) {
                let currentLatituede = position.coords.latitude,
                    currentLongitude = position.coords.longitude;
                var localStoreData = localStorage.getItem("store-distances");
                localStoreData = JSON.parse(localStoreData)
                if(localStoreData != null && localStoreData.currentPosition.latituede === currentLatituede && localStoreData.currentPosition.longitude === currentLongitude){
                    if(localStoreData.stores){
                        let setlocation = false;
                        $.each(localStoreData.stores,function(key,value){
                            if(value.locationCode === location.pickup_location_code){
                                var elem = $('.location-distance-'+location.pickup_location_code); 
                                elem.text(value.distance);
                                setlocation = true;
                                return false;
                            }
                        })
                        if(setlocation){
                            return true;
                        }
                    }
                } else {
                    let currentPosition = {"latituede": currentLatituede, "longitude": currentLongitude};
                    localStorage.setItem("store-distances", JSON.stringify({"currentPosition" : currentPosition}));
                }

                var origin = new google.maps.LatLng(currentLatituede, currentLongitude);
                var destination = new google.maps.LatLng(location.latitude, location.longitude);
                var service = new google.maps.DistanceMatrixService();
                var km = service.getDistanceMatrix(
                  {
                    origins: [origin],
                    destinations: [destination],
                    travelMode: 'DRIVING',
                  }, callback);

                function callback(response, status) {
                    let distace = response.rows[0].elements[0].distance.text;
                    var localStoreData = localStorage.getItem("store-distances");
                    localStoreData = JSON.parse(localStoreData)
                    var store = {"locationCode": location.pickup_location_code, "distance": distace}
                    if(localStoreData.stores){
                        localStoreData.stores.push(store)
                    }else{
                        localStoreData['stores'] = [];
                        localStoreData.stores.push(store)
                    }
                    localStorage.setItem("store-distances", JSON.stringify(localStoreData));
                    var elem = $('.location-distance-'+location.pickup_location_code); 
                    elem.text(distace);
                }
                return true;
            }
        },

        /**
         * @param {String} searchQuery
         * @returns {*}
         */
        updateNearbyLocations: function (searchQuery) {
            var self = this,
                productsInfo = [],
                items = quote.getItems(),
                searchCriteria;

            _.each(items, function (item) {
                if (item['qty_options'] === undefined || item['qty_options'].length === 0) {
                    productsInfo.push(
                        {
                            sku: item.sku
                        }
                    );
                }
            });

            searchCriteria = {
                extensionAttributes: {
                    productsInfo: productsInfo
                },
                pageSize: this.nearbySearchLimit
            };

            if (searchQuery) {
                searchCriteria.area = {
                    radius: this.nearbySearchRadius,
                    searchTerm: searchQuery
                };
            }

            return pickupLocationsService
                .getNearbyLocations(searchCriteria)
                .then(function (locations) {
                    self.nearbyLocations(locations);
                })
                .fail(function () {
                    self.nearbyLocations([]);
                });
        },

        /**
         * @returns {Boolean}
         */
        validatePickupInformation: function () {
            var emailValidationResult,
                loginFormSelector = this.loginFormSelector;

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = $(loginFormSelector + ' input[name=username]').valid() ? true : false;

                if (!emailValidationResult) {
                    $(this.loginFormSelector + ' input[name=username]').trigger('focus');

                    return false;
                }
            }

            return true;
        }
    });
});
