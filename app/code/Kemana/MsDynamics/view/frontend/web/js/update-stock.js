/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

define([    
    'jquery'
], function ($) {
    'use strict';
    $.widget('mage.stoackupdate', {
        options: {

            // option's url
            url: '',

            // option's productid
            productid: '',

            // option's producttype
            producttype: '',
        },

        _init: function () {       
            var $widget = this;
            $widget._EventListener();
            
            if($widget.options.productid){
                $widget._callAPI($widget);
            }
        },

        _create: function() {

        },

        _EventListener: function() {
            var $widget = this;
            
        },

        // Price and availability SAP API call
        _callAPI: function($widget){
            $.ajax({
                url: $widget.options.url,
                type: 'POST',
                cache: true,
                data:{
                  id: $widget.options.productid
                },
                success: function(response) {
                    if(response){
                        if($widget.options.producttype == 'simple'){
                           if(response.response.Inventory > 0){
                                $(".box-tocart").show();
                                $(".product.alert.stock").hide();
                                $(".product-info-stock-sku .stock").addClass("available").html("<span>In stock</span>")
                           }else{
                                $(".box-tocart").hide();
                                $(".product.alert.stock").show();
                                $(".product-info-stock-sku .stock").addClass("unavailable").html("<span>Out of stock</span>")
                           }
                        }
                    }else{
                        $(".box-tocart").hide();
                        $(".product.alert.stock").show();
                        $(".product-info-stock-sku .stock").addClass("unavailable").html("<span>Out of stock</span>")
                    }
                }
            });
        }

    });

    return $.mage.stoackupdate;
});