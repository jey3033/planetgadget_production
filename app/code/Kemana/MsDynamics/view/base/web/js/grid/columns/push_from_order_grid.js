define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/modal/modal'
], function (Column, $, uiRegistry) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        getOrderId: function (row) {
            return row.synced_with_erp_orderId;
        },
        getAjaxUrl: function (row) {
            return row.synced_with_erp_ajaxUrl;
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },

        refreshTable : function (params) {
            uiRegistry.get('index = sales_order_grid').source.reload({refresh: true})
        },

        syncOrdersToErp: function (row) {
            const self = this;
            const orderId = this.getOrderId(row);
            const url = this.getAjaxUrl(row);

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    orderId: orderId,
                    callingFrom: 'orderGrid',
                },
                showLoader: true,
                complete: function(response) {
                    if (response.responseJSON.result.result) {
                        self.refreshTable();
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error Refresh and re try.');
                }
            });

        },
        getFieldHandler: function (row) {
            return this.syncOrdersToErp.bind(this, row);
        }
    });
});
