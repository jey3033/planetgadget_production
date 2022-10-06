/**
 * KS_Logistic
 *
 * @see README.md
 *
 */
define([
    'jquery',   
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rates-validator'
], function ($, Abstract, modal, quote, rateRegistry, rateValidator) {
    'use strict';

    return Abstract.extend({
        defaults: {
            modalMapId: '',
            locationName: '',
            mapId: '',
            flagInit: 0,
            gmap: null,
            markers: [],
            autocompleteName: '',
            autocompleteMap: null

        },

        initialize: function () {

            this._super();
            this.modalMapId = 'modal-map-' + this.uid;
            this.locationName = 'location-name-' + this.uid;
            this.mapId = 'map-' + this.uid;
            this.autocompleteName = 'autocomplete-' + this.uid;
            return this;
        },

        saveLatLngToQuote: function() {
            this.refreshRates();
        },

        refreshRates: function()
        {
            var address = quote.shippingAddress();
            if(!address.hasOwnProperty('customAttributes') || !address.customAttributes){
                address.customAttributes = {
                    map_point : { 
                        attribute_code: "map_point", 
                        value: this.value()
                    }
                };
            }
            if(!address.customAttributes.hasOwnProperty('map_point')){
                address.customAttributes.map_point = { 
                    attribute_code: "map_point", 
                    value: this.value()
                };
            }

            address.customAttributes.map_point.value = this.value();


            rateRegistry.set(address.getCacheKey(), null);

            var type = quote.shippingAddress().getType();
            if (type == 'new-customer-address') {
                rateValidator.validateFields();
            }
        },

        deleteMarkers: function() {
            this.clearMarkers();
            this.markers = [];
        },

        clearMarkers: function() {
            this.setMapOnAll(null);
        },

        setMapOnAll: function(mapopt) {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(mapopt);
            }
        },    

        addMarker: function(location) {
            var self = this;
            this.deleteMarkers();
            
            const iconBase = "/media/kemana/kslocator/";

            var icon = {
                 url: iconBase + 'PinpointLocation2x.png', // url
                 scaledSize: new google.maps.Size(48, 65), // size
                 origin: new google.maps.Point(0,0)
             };

            var marker = new google.maps.Marker({
                position: location,
                map: self.gmap,
                animation: google.maps.Animation.DROP,
                icon: icon
            });

            this.markers.push(marker);
        },

        initMapGosend: function() {
            var self = this;
            if (navigator.geolocation && location.protocol == 'https:') {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    if(!self.value()) {
                        self.value(position.coords.latitude+','+position.coords.longitude);
                    }

                    var latlng = new google.maps.LatLng(pos.lat, pos.lng);
                    self.gmap = new google.maps.Map(document.getElementById(self.mapId), {
                        center: latlng,
                        zoom: 12
                    });
                    self.gmap.addListener('click', function(event) {
                        self.value(event.latLng.lat()+','+event.latLng.lng());
                        self.addMarker(event.latLng);
                    });
                    self.addMarker(pos);
                    self.gmap.setCenter(pos);
                })
            }else{
                this.gmap = new google.maps.Map(document.getElementById(self.mapId), {
                    center: {lat: -6.176420, lng: 106.826806},
                    zoom: 12
                });
                this.gmap.addListener('click', function(event) {
                    self.value(event.latLng.lat()+','+event.latLng.lng());
                    self.addMarker(event.latLng);
                });
            }
        },

        initMapAutoComplete: function(){
            var self = this;
            self.autocompleteMap = new google.maps.places.Autocomplete(
                (document.getElementById(self.autocompleteName)),
                { types: ['geocode'] }
            );

            google.maps.event.addListener(self.autocompleteMap, 'place_changed', function() {

                var place = self.autocompleteMap.getPlace();

                $('#'+this.locationName).val(place.formatted_address);
                self.value(place.geometry.location.lat()+','+place.geometry.location.lng());

                var pos = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };
                self.addMarker(pos);
                self.gmap.setCenter(pos);
            });

        },        

        getButtonLabel: function(){
        	return this.buttonLabel;
        },

        openMap: function(){
            var self = this;
            if(!this.flagInit){
                this.initMapGosend();
                this.initMapAutoComplete();
                this.flagInit = 1;
            }

            var modalOptions = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: false,
                        title: 'Choose Location',
                        modalClass: 'gmap-location',
                        buttons: [{
                            text: $.mage.__('Confirm Location'),
                            class: 'pinpoint-map-confirm',
                            click: function () {
                                this.closeModal();
                                self.saveLatLngToQuote();
                            }
                        }]
                    };            
            var popup = modal(modalOptions, $('#' + this.modalMapId));
            $('#' + this.modalMapId).modal("openModal"); 
        }
    });
});
