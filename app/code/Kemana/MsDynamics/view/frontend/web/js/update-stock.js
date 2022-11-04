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
                $(".product.alert.stock").addClass("stock-loader");
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
                    if(response.msDynamics){
                        if(typeof response.apiresponse.totalStock != 'undefined' && $widget.options.producttype == 'simple'){
                                let instock = false;
                                $.each(response.apiresponse.totalStock,function(sku,qty){
                                    if(qty > 0){
                                        instock = true;
                                    }
                                })
                                if(instock){
                                    $(".product-add-form").show();
                                    $(".product.alert.stock").hide();
                                    $(".product-info-stock-sku .stock").addClass("available").html("<span>In stock</span>")
                                }else{
                                    $(".product-add-form").hide();
                                    $(".product.alert.stock").show();
                                    $(".product-info-stock-sku .stock").addClass("unavailable").html("<span>Out of stock</span>")
                                }
                        }else if((typeof response.apiresponse.totalStock == 'undefined' || response.apiresponse.totalStock == null) && response.instock){
                            $(".product-add-form").show();
                            $(".product.alert.stock").hide();
                        }else{
                            $(".product-add-form").hide();
                            $(".product.alert.stock").show();
                            $(".product-info-stock-sku .stock").addClass("unavailable").html("<span>Out of stock</span>")
                        }
                    }else{
                        $(".product-add-form").show();
                        $(".product.alert.stock").show();
                    }
                    $(".product.alert.stock").removeClass("stock-loader");
                }
            });
        }

    });

    return $.mage.stoackupdate;
});