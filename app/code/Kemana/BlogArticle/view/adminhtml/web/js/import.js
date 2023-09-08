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
    'jquery'
], function ($) {
    'use strict';

    var dataImport = {};
    var defaultTablePrefix = ['wp_', 'm1_', 'm2_'];
    var imagePathHint = [
        '*Please copy the <b>wp_content/uploads</b> folder to <b>pub/media/wysiwyg/</b> folder for post image content <br>' +
        '*Please copy the <b>wp_content/uploads</b> folder to <b>pub/media/kemana/blog/post/</b> folder for post image banner <br>',
        '*Please copy the <b>media</b> folder to <b>pub/media/</b> folder',
        '*Please copy the <b>pub/media/magefan_blog</b> folder to <b>pub/media/kemana/blog/post/</b> folder for post image banner '
    ];
    var typeSelector = [];
    var validateImportUrl = '';
    var importUrl = '';
    var errorMsg = '';
    var successMsg = '';

    $.widget('kemana.mpBlogImport', {
            options: {
                typeSelector: '',
                validateImportUrl: '',
                importUrl: '',
                errorMsg: '',
                successMsg: ''
            },
            _create: function () {
                var self = this;

                self.errorMsg = self.options.errorMsg;
                self.successMsg = self.options.successMsg;

                $('#import_type').on('change', function () {
                    self.typeSelector = self.options.typeSelector;
                    self.initImportFieldsSet();
                });

                $('#check-connection').on('click', function () {
                    self.validateImportUrl = self.options.validateImportUrl;
                    self.initImportCheckConnection(self.errorMsg, self.successMsg);
                });      

                $('#word-press-import').on('click', function () {
                    self.importUrl = self.options.importUrl;
                    self.importAction();
                });          
            },

            initImportFieldsSet: function () {
                for (var i = 0; i < this.typeSelector.length; i++) {
                    if ($('#import_type').attr("value") == this.typeSelector[i]) {
                        this.showFieldSet('#' + this.typeSelector[i] + '_fieldset');
                        dataImport = {
                            type: this.typeSelector[i],
                            importName: '#' + this.typeSelector[i] + '_import_name',
                            dbName: '#' + this.typeSelector[i] + '_db_name',
                            userName: '#' + this.typeSelector[i] + '_user_name',
                            pwd: '#' + this.typeSelector[i] + '_db_password',
                            host: '#' + this.typeSelector[i] + '_db_host',
                            tablePrefix: '#' + this.typeSelector[i] + '_table_prefix',
                            behaviourSelector: '#' + this.typeSelector[i] + '_import_behaviour',
                            expandBehaviourSelector: '#' + this.typeSelector[i] + '_import_behaviour_expand'
                        };

                        $(dataImport.tablePrefix).val(defaultTablePrefix[i]);
                        $("#" + this.typeSelector[i] + "_import_image_path").html(imagePathHint[i]);
                        $("#" + this.typeSelector[i] + "_db_name").addClass('required-entry');
                        $("#" + this.typeSelector[i] + "_user_name").addClass('required-entry');
                        $("#" + this.typeSelector[i] + "_db_host").addClass('required-entry');
                    } else {
                        this.hideFieldSet('#' + this.typeSelector[i] + '_fieldset');
                        $("#" + this.typeSelector[i] + "_db_name").removeClass('required-entry');
                        $("#" + this.typeSelector[i] + "_user_name").removeClass('required-entry');
                        $("#" + this.typeSelector[i] + "_db_host").removeClass('required-entry');
                    }
                }
            },

            initExpandBehaviour: function () {
                if ($(dataImport.behaviourSelector).attr("value") == 'update') {
                    $(dataImport.expandBehaviourSelector).parent().parent().removeClass('hidden');
                } else {
                    $(dataImport.expandBehaviourSelector).parent().parent().addClass('hidden');
                }
            },

            showFieldSet: function (selector) {
                $(selector).show();
            },

            hideFieldSet: function (selector) {
                $(selector).hide();
            },

            initImportCheckConnection: function (errorMsg, successMsg) {
                if ($('#edit_form').valid()) {
                    $('body').loader('show');
                    $.ajax({
                        url: this.validateImportUrl,
                        data: {
                            type: dataImport.type,
                            import_name: $(dataImport.importName).val(),
                            database: $(dataImport.dbName).val(),
                            user_name: $(dataImport.userName).val(),
                            password: $(dataImport.pwd).val(),
                            host: $(dataImport.host).val(),
                            table_prefix: $(dataImport.tablePrefix).val(),
                            behaviour: $(dataImport.behaviourSelector).val(),
                            expand_behaviour: $(dataImport.expandBehaviourSelector).val()
                        },
                        cache: false,
                        success: function (result) {
                            var messageHtml;
                            if (result.status == 'false') {
                                messageHtml =  errorMsg;
                                $(".message-error").hide();
                                $(messageHtml).appendTo($(".page-columns"));
                                $(".message-success").hide();
                            } else {
                                messageHtml = successMsg;
                                $(".message-success").hide();
                                $(messageHtml).appendTo($(".page-columns"));
                                $(".message-error").hide();
                            }
                        },
                        complete: function () {
                            $('body').loader('hide');
                        }
                    });
                }
            },

            importAction: function () {
                $('body').loader('show');
                $.ajax({
                    url: this.importUrl,
                    cache: false,
                    success: function (result) {
                        var statisticMessage = result.statistic;
                        $(".message-success").hide();
                        $(".message-error").hide();
                        $(statisticMessage).appendTo($(".page-columns"));
                    },
                    complete: function () {
                        $('body').loader('hide');
                    }
                });
            }

        }
    );

    return $.kemana.mpBlogImport;
});
