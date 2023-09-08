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
        "jquery", 'jquery/ui', "mage/mage", "mage/translate"
], function ($) {
    'use strict';

    $.widget('kemana.categoryEditForm', {
            options: {
                refreshUrl: ''
            },
            
            _create: function () {
                var self = this;

                var mageDialog = (function ($) {
                    var self = {dialogOpened: false, callback: [], needShow: false};

                    self.callback = {ok: [], cancel: []};
                    self.createDialog = function () {
                        var onEvent = function (type, dialog) {
                            self.callback[type].forEach(function (call) {
                                call();
                            });
                            $(dialog).dialog("close");
                        };

                        self.dialog = $('[data-id="information-dialog-category"]').dialog({
                            autoOpen: false,
                            modal: true,
                            dialogClass: 'popup-window',
                            resizable: false,
                            width: '75%',
                            title: $.mage.__('Warning message'),
                            buttons: [{
                                text: $.mage.__('Ok'),
                                'class': 'action-primary',
                                click: function () {
                                    onEvent('ok', this);
                                }
                            }, {
                                text: $.mage.__('Cancel'),
                                'class': 'action-close',
                                click: function () {
                                    onEvent('cancel', this);
                                }
                            }],
                            open: function () {
                                $(this).closest('.ui-dialog').addClass('ui-dialog-active');

                                var topMargin = $(this).closest('.ui-dialog').children('.ui-dialog-titlebar').outerHeight() + 30;
                                $(this).closest('.ui-dialog').css('margin-top', topMargin);

                                self.dialogOpened = true;
                                self.callback.ok.push(function () {
                                    self.needShow = false;
                                });
                            },
                            close: function (event, ui) {
                                $(this).dialog('destroy');
                                self.dialogOpened = false;
                                self.callback = {ok: [], cancel: []};
                                delete self.dialog;
                            }
                        });
                    };

                    return {
                        needToShow: function () {
                            self.needShow = true && !!$('[data-ui-id="tabs-tab-general-information-fieldset-element-hidden-general-id"]').length;
                            return this;
                        },
                        isNeedShow: function () {
                            return self.needShow;
                        },
                        onOk: function (call) {
                            self.callback.ok.push(call);
                            return this;
                        },
                        onCancel: function (call) {
                            self.callback.cancel.push(call);
                            return this;
                        },
                        show: function () {
                            if (self.dialog == undefined) {
                                self.createDialog();
                            }
                            if (self.dialogOpened == false) {
                                self.dialog.dialog('open');
                            }
                            return this;
                        }
                    };
                })($);
                
                $(document).on('change', '[data-ui-id="urlkeyrenderer-text-general-url-key"]', function () {
                    mageDialog.needToShow();
                });

                $('#category_edit_form')
                    .mage('categoryForm', {refreshUrl: self.options.refreshUrl})
                    .mage('validation', {
                        submitHandler: function (form) {
                            if (mageDialog.isNeedShow()) {
                                mageDialog.onOk(function () {
                                    form.submit();
                                    $('body').loadingPopup();
                                }).show();
                            } else {
                                form.submit();
                                $('body').loadingPopup();
                            }
                        }
                    });
                   
            }

        }
    );

    return $.kemana.categoryEditForm;
});
