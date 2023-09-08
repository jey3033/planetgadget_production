/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Blog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   kemana team <jakartateam@kemana.com>
 */
 
define([
    'Magento_Ui/js/grid/columns/select'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },
        getLabel: function (record) {
            var label = this._super(record);

            if (label !== '') {
                switch (record.status) {
                    case '1':
                        label = '<span class="grid-severity-notice"><span>' + label + '</span></span>';
                        break;
                    case '2':
                        label = '<span class="grid-severity-critical"><span>' + label + '</span></span>';
                        break;
                    case '3':
                        label = '<span class="grid-severity-minor"><span>' + label + '</span></span>';
                        break;
                }
            }
            return label;
        }
    });
});
