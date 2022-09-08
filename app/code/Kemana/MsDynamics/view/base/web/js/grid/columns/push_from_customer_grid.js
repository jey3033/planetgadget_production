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
        getCustomerId: function (row) {
            return row.synced_with_erp_customerId;
        },
        getAjaxUrl: function (row) {
            return row.synced_with_erp_ajaxUrl;
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },

        refreshTable : function (params) {
            uiRegistry.get('index = customer_listing').source.reload({refresh: true})
        },

        syncCustomerToErp: function (row) {
            const self = this;
            const customerId = this.getCustomerId(row);
            const url = this.getAjaxUrl(row);

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    customerId: customerId,
                    callingFrom: 'customerGrid',
                },
                showLoader: true,
                complete: function(response) {
                    if (response.responseJSON.result.result &&
                        response.responseJSON.result.msDynamicCustomerNumber) {
                        self.refreshTable();
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error Refresh and re try.');
                }
            });

        },
        getFieldHandler: function (row) {
            return this.syncCustomerToErp.bind(this, row);
        }
    });
});
