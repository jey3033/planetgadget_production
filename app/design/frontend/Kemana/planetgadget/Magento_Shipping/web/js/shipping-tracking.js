/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    "use strict";

    $.widget('mage.shippingTracking', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            responsive: true,
            messagesSelector: '[data-placeholder="messages"]',
            type: 'popup',
            modalClass: 'promotion-popup shipping-tracking-popup',
            buttons: []
        },

        _create: function () {
            this._bindTrackingLink();
        },

        _bindTrackingLink: function () {
            var self = this;
            this.element.on('click', function (e) {
                self.ajaxTrackingPopup($(this));
            });
        },

        /**
         * Handler ajax tracking popup
         *
         * @param {Object} form
         */
        ajaxTrackingPopup: function (form) {
            var self = this;
            var tracking_hash_url = form.attr('tracking-hash-url');
            $.ajax({
                url: tracking_hash_url,
                type: 'GET',
                success: function (data) {
                    $('#pg-tracking-popup-content').html($(data).find('.page.tracking').html());
                    $("#pg-tracking-popup-content").find('.action.close').remove();
                    var modal_popup_element = $('#pg-tracking-popup');
                        modal_popup_element.modal(self.options).modal('openModal');
                }
            });
        }

    });
    return $.mage.shippingTracking;
});
