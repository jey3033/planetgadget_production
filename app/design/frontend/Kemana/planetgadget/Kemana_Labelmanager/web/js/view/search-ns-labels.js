/**
 * Kemana Pvt Ltd.
 *
 * @category    Kemana
 * @package     Kemana_Labelmanager
 * @author      Kemana Team <contact@kemana.com>
 * @copyright   Copyright (c) Kemana Pvt Ltd.
 */
define([
    'jquery',
    'jquery/ui'
], function($) {
    'use strict';
    $.widget('ns.searchNsLabels', {
        options: {
            id: false,
            page:null,
            location: "",
            baseUrl: false,
            currency: false,
            productType: false
        },
        _create: function() {
            var self = this;
            var $search = $("#search");
            $search.on('typeahead:asyncreceive' , function () {
                $search.mage('nsLabels' , self.options)
            })
        },
    });

    return $.ns.searchNsLabels;
});





