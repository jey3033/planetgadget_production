/**
 * @category Kemana
 * @package  Kemana_HeaderTopLine
 * @license  Proprietary
 *
 * @author   Anton Vinoj <avinoj@kemana.com>
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('kemana.topHeader', {
        _create:function() {
            let self = this;
            self._toggleHelpLink();
        },
        _toggleHelpLink : function() {
            var $toggleButton = $('.help-info-toggle');
            var $content = $('.help-info-content');

            $toggleButton.click(function(event) {
                $content.slideToggle(600);
                $('.help-info-toggle').toggleClass("active");
            });
        }
    });

    return $.kemana.topHeader;
});
