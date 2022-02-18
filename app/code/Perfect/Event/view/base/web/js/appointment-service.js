define([
    'jquery',
    'uiElement',
    'ko',
    'mage/template',
    'Perfect_Event/js/view/appointment-template/service/default',
    'timetableConfig',
    'mage/collapsible'
], function($, Component, ko, mageTemplate, defaultTemplate, config) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perfect_Event/appointment/popup/service_new',
            appointmentTableContainer: '.appointment-service-table tbody'
        },
        initialize: function () {
            this._super();
        },
        addService: function () {
            var params = {data: {row_index: $(this.appointmentTableContainer).find('.data-row').length}};
            var templateText = $(mageTemplate(defaultTemplate())(params));

            $(this.appointmentTableContainer).append(templateText);

            this.initEvents(templateText);

            return this;
        },
        deleteService: function (event) {
            var self = this,
                target = $(event.target).closest('.data-row');

            target.fadeOut(200, function() {
                target.remove();

                var dataRows = $(self.appointmentTableContainer).find('.data-row');
                if (dataRows.length) {
                    dataRows.find('.service_name').each(function (index) {
                        $(this).attr('name', 'service_name[' + index + ']');
                    });
                }
            });
            return this;
        },
        increaseServiceQty: function (event) {
            var rowElement = $(event.target).closest('.data-row'),
                qtyElement = rowElement.find('.input-qty');

            if (qtyElement) {
                var qty = parseInt(qtyElement.val());
                qty = _.isNumber(qty) ? ++qty : 0;

                if (qty >= parseInt(qtyElement.attr('max'))) {
                    var plusElement = rowElement.find('.btn-qty-plus');
                    if (plusElement) {
                        plusElement.prop('disabled', true);
                    }
                    qty = parseInt(qtyElement.attr('max'));
                }
                qtyElement.val(qty);
            }
            var minusElement = rowElement.find('.btn-qty-minus');
            if (minusElement.prop('disabled')) {
                minusElement.prop('disabled', false);
            }
        },
        decreaseServiceQty: function (event) {
            var rowElement = $(event.target).closest('.data-row'),
                qtyElement = rowElement.find('.input-qty');

            if (qtyElement) {
                var qty = parseInt(qtyElement.val());
                qty = _.isNumber(qty) ? --qty : 0;

                if (qty <= parseInt(qtyElement.attr('min'))) {
                    var minusElement = rowElement.find('.btn-qty-minus');
                    if (minusElement) {
                        minusElement.prop('disabled', true);
                    }
                    qty = parseInt(qtyElement.attr('min'));
                }
                qtyElement.val(qty);
            }
            var plusElement = rowElement.find('.btn-qty-plus');
            if (plusElement) {
                plusElement.prop('disabled', false);
            }
        },
        initEvents: function (target) {
            $('.btn-delete', target).on('click', this.deleteService.bind(this));
            $('.btn-qty-plus', target).on('click', this.increaseServiceQty.bind(this));
            $('.btn-qty-minus', target).on('click', this.decreaseServiceQty.bind(this));
            this.initAutocomplete(target);
            /*$(this.appointmentTableContainer).find(".fieldset-wrapper").collapsible({
                "header": ".fieldset-wrapper-title",
                "content": ".admin__collapsible-content",
                "openedState": "_show",
                "closedState": "_hide",
                "active": true
            });*/
        },
        initAutocomplete: function (target) {
            $('input[name="service_name"]', target).autocomplete({
                minLength: 2,
                source: function(request, response) {
                    $.ajax( {
                        url: config.getSearchClientUrl(),
                        dataType: 'json',
                        data: {search: request.term},
                        success: function(results) {
                            if (!results.length) {
                                $("#no-results").text("Клиенты не найдены");
                            } else {
                                $("#no-results").empty();
                            }

                            response(results);
                        }
                    });
                },
                messages: {
                    noResults: 'Клиенты не найдены',
                    results: function (amount) {
                        return '';
                    }
                },
                select: function (event, ui) {
                    alert('ok');
                }
            });
        },
    });
});