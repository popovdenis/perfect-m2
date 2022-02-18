define([
    'jquery',
    'uiElement',
    'ko',
    'mage/template',
    'Perfect_Event/js/view/appointment-template/service/default',
    'mage/collapsible'
], function($, Component, ko, mageTemplate, defaultTemplate) {
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
            var templateText = $(mageTemplate(defaultTemplate())());

            $(this.appointmentTableContainer).append(templateText);

            this.initEvents(templateText);

            return this;
        },
        deleteService: function (event) {
            var target = $(event.target).closest('.data-row');
            target.fadeOut(200, function() { target.remove(); });
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
            $('.action-delete', target).on('click', this.deleteService.bind(this));
            $('.btn-qty-plus', target).on('click', this.increaseServiceQty.bind(this));
            $('.btn-qty-minus', target).on('click', this.decreaseServiceQty.bind(this));
            /*$(this.appointmentTableContainer).find(".fieldset-wrapper").collapsible({
                "header": ".fieldset-wrapper-title",
                "content": ".admin__collapsible-content",
                "openedState": "_show",
                "closedState": "_hide",
                "active": true
            });*/
        }
    });
});