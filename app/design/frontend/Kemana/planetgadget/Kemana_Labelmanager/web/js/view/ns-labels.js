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
    $.widget('ns.nsLabels', {
        options: {
            id: false,
            page:null,
            location: "",
            baseUrl: false,
            currency: false,
            productType: false,
            productLbl: ''
        },
        data : {
            labels: null,
            configs: null,
            products: null,
            pageLabelCount: false
        },

        _create: function() {
            var self = this;
            if (self.options.baseUrl) {
                $.ajax({
                    url: 'labelmanager/labels/file',
                    type: 'post',
                    dataType: 'json',

                    /** @param {Object} response */
                    success: function (response) {
                        self.showLabels(response);
                    },

                    /** set empty array if error occurs */
                    error: function (err) {
                        console.log('file url fetching error: '+err);
                    }
                });
            }
        },
        showLabels: function(file) {
            var self = this;
            if(file){
                /** loading data from json file **/
                $.getJSON(file)
                    .done(function (data) {
                        var parsedData = JSON.parse(data);
                        self.data.products = parsedData.labelProducts;
                        self.data.configs = parsedData.configs;
                        self.data.labels = parsedData.labels;
                        var parsedId = JSON.parse(self.options.id);
                        if(parsedId){
                            /** product id available in PDP page */

                            var currentProduct = self.data.products[self.options.id];
                            if(!currentProduct){
                                /** Products not having standard labels, might have sale label; passing sku as id */
                                currentProduct = { "id": parsedId, "sku": parsedId}
                            }
                            var ele = $('.product-info-main').find('.price-box');
                            var priceDetails = [];
                            priceDetails['oldPrice'] = 0;
                            priceDetails['finalprice'] = 0
                            var hasSpecialPrice = false;
                            /** shopthe look and grouped products not supposed to show sale label,
                             * validating other product types */
                            if(self.options.productType=='configurable') {
                                hasSpecialPrice = ele.find('.old-price').length;
                                if (hasSpecialPrice) {
                                    priceDetails['finalprice'] = ele.find('#product-price-'+parsedId).data('price-amount');
                                    priceDetails['oldPrice'] = ele.find('.old-price').find('#old-price-'+parsedId)
                                        .data('price-amount');
                                }
                            } else if (self.options.productType=='bundle') {
                                /** get max discount out of from-to prices */
                                hasSpecialPrice = ele.find('.old-price').length;
                                if (hasSpecialPrice) {
                                    var fromFinal = ele.find('.price-from .price-final_price #from-'+parsedId)
                                        .data('price-amount')
                                    var fromOld = ele.find('.price-from .old-price #old-price-'+parsedId)
                                        .data('price-amount');
                                    var toFinal = ele.find('.price-to .price-final_price #to-'+parsedId)
                                        .data('price-amount');
                                    var toOld = ele.find('.price-to .old-price #old-price-'+parsedId)
                                        .data('price-amount');
                                    if ((fromOld-fromFinal) > (toOld-toFinal)) {
                                        priceDetails['finalprice'] = fromFinal;
                                        priceDetails['oldPrice'] = fromOld;
                                    } else {
                                        priceDetails['finalprice'] = toFinal;
                                        priceDetails['oldPrice'] = toOld;
                                    }
                                }
                            } else {
                                hasSpecialPrice = ele.find('.special-price').length;
                                if (hasSpecialPrice) {
                                    priceDetails['finalprice'] = ele.find('#product-price-'+parsedId)
                                        .data('price-amount');
                                    priceDetails['oldPrice'] = ele.find('#old-price-'+parsedId).data('price-amount');
                                }
                            }
                            /** labels on shopthelook and grouped products are displayed on image container */
                            var container = $(self.element);
                            if (self.options.productType==="shopthelook" || self.options.productType==="grouped"){
                                if (self.data.configs.pdpContainer==="product.info.price"
                                    || self.data.configs.pdpContainer==="product.info.stock.sku") {
                                    container = $('.media');
                                }
                            }
                            var productLabels = self.getLabels(currentProduct, 'pdp', priceDetails);
                            container.append(productLabels);

                            /** calling for product labels hover events */
                            let productLbl = self.options.productLbl;
                            self._showTooltipMouseEnter(productLbl);
                            self._showTooltipMouseLeave(productLbl);

                            /** calling for related products */
                            var related = $('.products-related').length;
                            if (related) {
                                self.loadListing();
                            }
                        }else{
                            /** Product ID not available in Listing Pages */
                            self.loadListing();
                        }

                    })
                    .error(function(e){
                        console.log(e);
                    });
            }
        },
        loadListing: function () {
            var self = this;
            /** check for already labeled products */
            $('.price-box').not('.labeled').each( function () {
                var currentProductId = $(this).data('product-id');
                var currentProduct = self.data.products[currentProductId];
                if(!currentProduct){
                    /** Products not having standard labels, might have sale label; passing sku as id */
                    currentProduct = {"sku": currentProductId};
                }
                var hasSpecialPrice = $(this).find('.old-price').length;
                var priceDetails = [];
                if(hasSpecialPrice){
                    var isBundled = $(this).find('.price-from').length;
                    if (isBundled) {
                        var ele = $(this);
                        var fromFinal = ele.find('.price-from .price-final_price #from-'+currentProductId)
                            .data('price-amount')
                        var fromOld = ele.find('.price-from .old-price #old-price-'+currentProductId)
                            .data('price-amount');
                        var toFinal = ele.find('.price-to .price-final_price #to-'+currentProductId)
                            .data('price-amount');
                        var toOld = ele.find('.price-to .old-price #old-price-'+currentProductId)
                            .data('price-amount');
                        if ((fromOld-fromFinal) > (toOld-toFinal)) {
                            priceDetails['finalprice'] = fromFinal;
                            priceDetails['oldPrice'] = fromOld;
                        } else {
                            priceDetails['finalprice'] = toFinal;
                            priceDetails['oldPrice'] = toOld;
                        }
                    } else {
                        priceDetails['finalprice'] = $(this).find('#product-price-'+currentProductId)
                            .data('price-amount');
                        priceDetails['oldPrice'] = $(this).find('#old-price-'+currentProductId)
                            .data('price-amount');
                        if ($(".normal-price span").find('#product-price-'+currentProductId+'-compare-list-top').length > 0) {
                            priceDetails['finalprice'] = $(".normal-price span").find('#product-price-'+currentProductId+'-compare-list-top')
                                .data('price-amount');
                        }
                        if ($(".special-price span").find('#product-price-'+currentProductId+'-compare-list-top').length > 0) {
                            priceDetails['finalprice'] = $(".special-price span").find('#product-price-'+currentProductId+'-compare-list-top')
                                .data('price-amount');
                        }
                        if ($(".old-price span").find('#product-price-'+currentProductId+'-compare-list-top').length > 0) {
                            priceDetails['oldPrice'] = $(".old-price span").find('#product-price-'+currentProductId+'-compare-list-top')
                                .data('price-amount');
                        }
                    }

                }
                if(currentProduct.labels || hasSpecialPrice ){
                    var productLabels = self.getLabels(currentProduct, 'plp',priceDetails);

                    /** labels are supposed to show on image wrapper in listing page */
                    var $productImageWrapper  = $(this).closest('.product-item');

                    if($productImageWrapper.length){
                        $productImageWrapper.find(".product-image-photo").after(productLabels);
                    }else{
                        $(this).closest('.search-cnt').find('.image a').after(productLabels);
                    }

                }
                /** adding class to just labeled product */
                $(this).addClass('labeled');
            } );
        },
        getLabels: function (product, page,priceDetails) {
            var self = this;
            var labelList = [];
            var labelItemCount = 0;
            var rawLabelData = self.data.labels;
            var configs = self.data.configs;
            var hasSpecialPrice = false;

            if(configs.saleLabelBasedOnSpecPrice === true && priceDetails['oldPrice'] ){
                hasSpecialPrice = true;
            }
            var className = "ns-product-label ns-product-label-" +
                product.sku +
                " " +
                configs.displayPosition +
                " " +
                configs.displayStyle;

            labelList = "";
            if(hasSpecialPrice){
                var discText = configs.saleLabelText;
                if(!((configs.showSaleLabelDescPerc===false) && (configs.showSaleDiscAmnt===false))){
                    if(configs.showSaleLabelDescPerc) {
                        var amnt = ((priceDetails['oldPrice']
                            - priceDetails['finalprice'])/priceDetails['oldPrice'])*100;
                        if(!isNaN(amnt)) {
                            discText += " "+amnt.toFixed(0)+"%";
                        }
                    } else if (configs.showSaleDiscAmnt) {
                        var amnt2 = priceDetails['oldPrice'] - priceDetails['finalprice'];
                        if(!isNaN(amnt2)) {
                            discText += " " + self.options.currency + amnt2.toFixed(2);
                        }
                    }
                }
                if(discText){
                    labelList += "<li data-position='"+configs.saleLabelPosition+"' style='"+configs.salesCSS+"' >"
                        +discText+"</li>";
                }
            }
            if (page==='pdp') {
                self.data.pageLabelCount = self.data.configs.maxLabelsPdp;
            } else if (page==='plp') {
                self.data.pageLabelCount = self.data.configs.maxLabelsListing;
            }
            if (!$.isNumeric(self.data.pageLabelCount) || parseInt(self.data.pageLabelCount) < 1 ){
                self.data.pageLabelCount = 1;
            }
            if (self.data.labels) {
                $.each(product.labels , function (key,bLabel){
                    $.each(rawLabelData, function (innerKey, labelData) {
                        if(key === labelData.attribute_code && bLabel === true){
                            if ( self.data.pageLabelCount > labelItemCount ) {
                                if(page==='pdp' && labelData.image_product_url  && labelData.switch_to==="image") {
                                    labelList += "<li data-position='"+labelData.sort_order+"'>" +
                                        "<img src='"+labelData.image_category_url+"' /></li>";
                                } else if (page==="plp" && labelData.image_product_url && labelData.switch_to==="image")
                                {
                                    labelList += "<li  data-position='"+labelData.sort_order+"'>" +
                                        "<img src='"+labelData.image_category_url+"' /></li>";
                                } else {
                                    var labelText = (page==='pdp') ? labelData.product_label_text
                                        : labelData.category_label_text;
                                    if(!labelText){
                                        labelText = labelData.name;
                                    }

                                    if(labelData.product_label_tooltip_text){
                                        labelList += "<li style='background-color: "
                                            +labelData.background_color+"; color: "
                                            +labelData.font_color+"' data-position='"+labelData.sort_order+"'>"
                                            +"<span>"+labelText+"</span>"+"<div class='lbl-tooltip tp-"+labelText+"'><span>"+labelData.product_label_tooltip_text+"</span></div>"+"</li>";
                                    }
                                    else{
                                        labelList += "<li style='background-color: "
                                            +labelData.background_color+"; color: "
                                            +labelData.font_color+"' data-position='"+labelData.sort_order+"'>"
                                            +"<span>"+labelText+"</span></li>";
                                    }

                                }
                            }
                            labelItemCount++;
                        }
                    });

                });
            }

            var $sortedListItems = $(labelList).sort(this.sortListOrder);
            var listHtmlContent = "";
            $.each($sortedListItems.slice(0,parseInt(self.data.pageLabelCount)) , function (key,val){
                listHtmlContent += $(this).prop('outerHTML');
            });

            return "<ol class='"+className+"'>"+listHtmlContent+"</ol>";
        },
        sortListOrder : function (a,b) {
            return parseInt($(a).data('position')) -   parseInt($(b).data('position'));
        },
        _showTooltipMouseEnter: function(productLbl) {
            $(productLbl).each(function() {
                $(this).find('ol li').mouseenter(function() {
                    $(this).addClass('active');
                });
            });
        },

        _showTooltipMouseLeave: function(productLbl) {
            $(productLbl).each(function() {
                $(this).find('ol li').mouseleave(function() {
                    $(this).removeClass('active');
                });
            });
        },
    });

    return $.ns.nsLabels;
});
