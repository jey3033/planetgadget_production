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
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data'

], ($, createShippingAddress, selectShippingAddress, checkoutData) => function (Component) {
    return Component.extend({

        manualValidationFields: ['[name=city_id]', '[name=district_id]'], // Supports only select element selectors

        initialize() {
            this._super();
        },

        saveNewAddress: function () {
            var addressData,
                newShippingAddress;
            this.source.set('params.invalid', false);
            this.triggerShippingDataValidateEvent();

            if (this.hasInvalidManualFields()) {
                this.source.set('params.invalid', true);
            }


            if (!this.source.get('params.invalid')) {
                addressData = this.source.get('shippingAddress');
                // if user clicked the checkbox, its value is true or false. Need to convert.
                addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                // New address must be selected as a shipping address
                newShippingAddress = createShippingAddress(addressData);
                selectShippingAddress(newShippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                this.getPopUp().closeModal();
                this.isNewAddressAdded(true);
            }
        },

        hasInvalidManualFields() {
            const  parent = 'form.form-shipping-address';
            return this.manualValidationFields.map((fieldSelector) => {
                const $field = $(parent+' '+`${fieldSelector}`);
                const validated = $field.attr('aria-invalid') === 'true';
                const isEmpty = $field.val() === '';
                if (validated) {
                    return true;
                }
                if (isEmpty) {
                    const firstOption = $field.find('option:nth(1)').val();
                    $field.val(firstOption).trigger('change').val('').trigger('change');
                    return true;
                }

                return false;
            }).some((invalidedField) => invalidedField === true);
        }

    });
});
