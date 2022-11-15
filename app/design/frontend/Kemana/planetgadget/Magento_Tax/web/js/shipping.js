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
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function($, Component, quote) {
    'use strict';
    return function(shipping) {
        return shipping.extend({
            getValue: function() {
                var price;

                if (this.totals()['shipping_amount'] == 0) {
                    return this.notCalculatedMessage;
                }
                price = this.totals()['shipping_amount'];

                return this.getFormattedPrice(price);
            },
        });
    }
});