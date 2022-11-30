define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'mage/calendar'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            var disabled = window.checkoutConfig.shipping.delivery_date.disabled;
            var noday = window.checkoutConfig.shipping.delivery_date.noday;
            var format = null;
            if(!format) {
                format = 'dd-mm-yy';
            }
            var disabledDay = disabled.split(",").map(function(item) {
                return parseInt(item, 10);
            });

            ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    //initialize datetimepicker
                    if(noday) {
                        var options = {
                            minDate: 0,
                            showButtonPanel: true,
                            closeText: 'Clear',
                            clearBtn: true,
                            changeMonth: true,
                            monthNamesShort: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                            dateFormat:format
                        };
                    } else {
                        var options = {
                            minDate: 0,
                            dateFormat:format,
                            beforeShowDay: function(date) {
                                var day = date.getDay();
                                if(disabledDay.indexOf(day) > -1) {
                                    return [false];
                                } else {
                                    return [true];
                                }
                            }
                        };
                    }

                    $el.datepicker(options);

                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {
                            writable = propWriters.datetimepicker;
                        } else {
                            return;
                        }
                    }
                    writable($(element).datepicker("getDate"));
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    //when the view model is updated, update the widget
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                    }
                }
            };

            return this;
        }
    });
});
