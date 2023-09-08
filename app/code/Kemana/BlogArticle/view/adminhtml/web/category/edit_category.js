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
        "jquery",
        "tinymce4",
        "loadingPopup",
        "mage/backend/floating-header"
], function ($, tinyMCE) {
    'use strict';

    $.widget('kemana.categoryEdit', {
            options: {
                resetUrl: '',
                deleteUrl: '',
                confirmationDeleteMsg: ''
            },
            _create: function () {
                var self = this;

                // window.refreshTreeArea = self.refreshTreeArea();
                // window.updateContent = self.updateContent();
                // window.categoryDelete = self.categoryDelete();
                // window.categoryReset = self.categoryReset();
                // window.displayLoadingMask = self.displayLoadingMask();

                $('#reset').on('click', function () {
                    self.categoryReset(self.options.resetUrl,false);
                });

                $('#delete').on('click', function () {
                    self.categoryDelete(self.options.deleteUrl,self.options.confirmationDeleteMsg);
                });                        
            },

            categoryReset: function (url, useAjax) {
                if(url){
                    if (useAjax) {
                        var params = {active_tab_id: false};
                        updateContent(url, params);
                    } else {
                        location.href = url;
                    }                    
                }
            },

            categoryDelete: function (url, confirmationDeleteMsg) {
                if (confirm(confirmationDeleteMsg)) {
                    location.href = url;
                }
            },

            updateContent: function(url, params, refreshTree){
                var node = tree.getNodeById(tree.currentNodeId),
                    parentNode = node && node.parentNode,
                    parentId,
                    redirectUrl;

                params = $.extend(params || {}, {
                    form_key: FORM_KEY
                });

                if (params.node_name) {
                    node.setText(params.node_name);
                }

                if(url){
                    (function ($) {
                        var $categoryContainer = $('#category-edit-container'),
                            messagesContainer = $('.messages');
                        messagesContainer.html('');
                        $.ajax({
                            url: url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true'),
                            data: params,
                            context: $('body'),
                            showLoader: true
                        }).done(function (data) {
                            if (data.content) {
                                var pageActions = $('.page-actions');
                                for (var i = 0; i < pageActions.length; i++) {
                                    if ($(pageActions[i]).data('floatingHeader')) {
                                        $(pageActions[i]).floatingHeader('destroy');
                                    }
                                }
                                try {
                                    $('#category-edit-container .wysiwyg-editor').each(function (index, element) {
                                        tinyMCE.execCommand('mceRemoveControl', false, $(element).attr('id'));
                                    });
                                    $categoryContainer.html('');
                                } catch (e) {
                                    alert(e.message);
                                }
                                $categoryContainer.html(data.content).trigger('contentUpdated');
                                setTimeout(function () {
                                    $('#category-edit-container .wysiwyg-editor').each(function (index, element) {
                                        tinyMCE.execCommand('mceRemoveControl', false, $(element).attr('id'));
                                        tinyMCE.execCommand('mceAddControl', true, $(element).attr('id'));
                                    });
                                    $('.page-actions').floatingHeader({
                                        'title': '.category-edit-title'
                                    });
                                    $('body').trigger('contentUpdated');
                                    try {
                                        if (refreshTree) {
                                            self.refreshTreeArea();
                                        }
                                    } catch (e) {
                                        alert(e.message);
                                    }
                                }, 25);
                            }

                            if (data.messages && data.messages.length > 0) {
                                messagesContainer.html(data.messages);
                            }
                            if (data.toolbar) {
                                $('[data-ui-id="page-actions-toolbar-content-header"]').replaceWith(data.toolbar)
                            }
                        });
                    })($);                    
                }
            },

            refreshTreeArea: function(transport){
                var config,
                    url;

                if (tree && window.editingCategoryBreadcrumbs) {
                    if (tree.nodeForDelete) {
                        var node = tree.getNodeById(tree.nodeForDelete);
                        tree.nodeForDelete = false;

                        if (node) { // Check maybe tree became somehow not synced with ajax and we're trying to delete unknown node
                            node.parentNode.removeChild(node);
                            tree.currentNodeId = false;
                        }
                    }
                    // category created - add its node
                    else if (tree.addNodeTo) {
                        var parent = tree.getNodeById(tree.addNodeTo);
                        tree.addNodeTo = false;

                        if (parent) { // Check maybe tree became somehow not synced with ajax and we're trying to add to unknown node
                            config = editingCategoryBreadcrumbs[editingCategoryBreadcrumbs.length - 1];

                            window.location.href = tree.buildUrl(config.id);
                        }
                    }

                    // update all affected categories nodes names
                    for (var i = 0; i < editingCategoryBreadcrumbs.length; i++) {
                        var node = tree.getNodeById(editingCategoryBreadcrumbs[i].id);
                        if (node) {
                            node.setText(editingCategoryBreadcrumbs[i].text);
                        }
                    }
                }                
            }

        }
    );

    return $.kemana.categoryEdit;
});
