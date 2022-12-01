/**
 * Copyright Â© 2020 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Catalog
 * @license  Proprietary
 *
 * @author   lakshitha <jlakshitha@kemana.com>
 */

define([
    'jquery',
    'utility',
], function ($,utility) {
    $.widget('kemana.customDropDown', {
        options: {
            ajax: false,
            responsive:false
        },

        _create: function () {
            let self = this;
            if(this.options.responsive && $(document.body).width() < 1024){
                self.element.hide();
            }
            if (self.element.find('.item-main').text() == "") {
                self.element.find('.item-main').text(self.element.find('.item').first().text());
            }
            self.element.find('.item').first().addClass('active');
            self.element.on('click', function () {
                self.element.find('.qty-selector-dropdown').toggle();
            });

            self.element.find('.item').on('click', function () {
                self.element.find('.item').removeClass('active');
                $(this).addClass('active');
                let value = $(this).attr('data-item');
                $(this).parents('.qty-selector').siblings('.qty-input').val(value);
                if (self.options.ajax) {
                    self._updateSideBar();
                } else {
                    self.element.find('.item-main').text(value);
                }
            });

            self.element.find('button').on('click', function () {
                var $this = $(this);
                var ctrl = ($(this).attr('id').replace('-upt','')).replace('-dec','');
                var currentQty = $("#cart-"+ctrl+"-qty").val();
                if($this.hasClass('increaseQty')){
                    var newAdd = parseInt(currentQty)+parseInt(1);
                    $("#cart-"+ctrl+"-qty").val(newAdd);
                }else{
                    if(currentQty>1){
                        var newAdd = parseInt(currentQty)-parseInt(1);
                        $("#cart-"+ctrl+"-qty").val(newAdd);
                    }
                }
                self._updateSideBar();


            });
        },
        _updateSideBar: function () {
            let self = this;
            let url = self.element.parents('form').attr('action');
            let formData = self.element.parents('form').serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                showLoader: true,
                success: function (response) {
                    let parsedRes = $.parseHTML(response);
                    let resultView = $(parsedRes).find('#shopping-cart-table');
                    $('#shopping-cart-table').replaceWith(resultView);

                    //reload summary total section
                    require(['Magento_Checkout/js/action/get-totals',
                        'Magento_Customer/js/customer-data'], function (getTotalsAction, customerData) {
                        var sections = ['cart'];

                        // The mini cart reloading
                        customerData.reload(sections, true);

                        // The totals summary block reloading
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);

                        //Display error if found after jquery
                        var messages = $.cookieStorage.get('mage-messages');
                        if (!_.isEmpty(messages)) {
                            customerData.set('messages', {messages: messages});
                            $.cookieStorage.set('mage-messages', '');
                        }
                    });

                },
                error: function () {
                    console.log('error!!');
                }
            })

        }
    });

    return $.kemana.customDropDown

})
