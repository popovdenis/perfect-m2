define([
    'jquery',
    'uiElement',
    'mage/template',
    'Perfect_Service/js/view/template/default',
    'Perfect_Service/js/model/serviceManager',
    'Perfect_Event/js/model/config',
    'Perfect_Event/js/storage',
    'ko'
], function($, Component, mageTemplate, defaultTemplate, serviceManager, config, storage, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perfect_Service/service_new',
            eventTableContainer: '.event-service-table tbody',
            listVisible: ko.observable(false),
            inputValue: '',
            multiselectFocus: false,
            hoverClass: '_hover',
            listens: {
                listVisible: 'cleanHoveredElement'
            }
        },
        initialize: function () {
            this._super();
        },
        initObservable: function () {
            this._super();
            this.observe([
                'listVisible',
                'inputValue',
                'loading',
                'multiselectFocus'
            ]);

            return this;
        },
        /**
         * Clean hoveredElement variable
         *
         * @returns {Object} Chainable
         */
        cleanHoveredElement: function () {
            return this;
        },
        addService: function () {
            var params = {data: {row_index: $(this.eventTableContainer).find('.data-row').length}};
            var templateText = $(mageTemplate(defaultTemplate())(params));

            $(this.eventTableContainer).append(templateText);

            var self = this;
            serviceManager().getServices(storage.currentEvent().extendedProps.employee_id, function (services) {
                var dropdown = '',
                    servicesSearchItems = templateText.find('.services-search-items');

                for (const index in services) {
                    if (services.hasOwnProperty(index)) {
                        let service = services[index];
                        let serviceDuration = service.service_duration_h + ' ч';
                        if (parseInt(service.service_duration_m)) {
                            serviceDuration += ' ' + service.service_duration_m + ' м';
                        }
                        let servicePrice = parseInt(service.service_price_from);
                        if (parseInt(service.is_price_range)) {
                            servicePrice += ' - ' + service.service_price_to;
                        }

                        dropdown +=
                            '<li class="admin__action-multiselect-menu-inner-item _root" data-role="option-group">\n' +
                                '<div class="action-menu-item service-details" data-service-index="' + service.service_id + '">\n' +
                                    '<span class="service-name" style="font-weight: bold;margin-right: 5px;">' + service.service_name + '</span>' +
                                    '<span class="service_details">' +
                                        '<span class="service_length" style="margin-right: 5px;">' + serviceDuration + '</span>' +
                                        '<span class="service_price">' + servicePrice + ' ₴</span>' +
                                    '</span>' +
                                '</div>\n' +
                            '</li>';

                        servicesSearchItems.append(dropdown);
                    }
                }

                self.initEvents(templateText);
            });

            return this;
        },
        deleteService: function (event) {
            var self = this,
                target = $(event.target).closest('.data-row');

            target.fadeOut(200, function() {
                target.remove();

                var dataRows = $(self.eventTableContainer).find('.data-row');
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
        initServicesDropdown: function (event) {
            if (!this.multiselectFocus()) {
                var target = event.currentTarget;
                if ($(target).hasClass('_active')) {
                    $(target).parent().removeClass('_active');
                    $(target).removeClass('_active');
                    $(target).find('.action-menu').removeClass('_active');
                } else {
                    $(target).parent().addClass('_active');
                    $(target).addClass('_active');
                    $(target).find('.action-menu').addClass('_active');
                }
            }
        },
        selectService: function (event) {
            var serviceId = $(event.currentTarget).data('service-index');
            if (typeof serviceId !== "undefined") {
                var currentEvent = storage.currentEvent(),
                    masterId = currentEvent.extendedProps.employee_id,
                    currentService = storage.getServiceById(masterId, serviceId);

                if (currentService && typeof currentService.service_name !== "undefined") {
                    $('.selected-service-value').text(currentService.service_name);

                    $('.service-price-summary').find('small').text(parseInt(currentService.service_price_from));

                    var serviceDuration = currentService.service_duration_h + ' ч.';
                    if (parseInt(currentService.service_duration_m)) {
                        serviceDuration += ' ' + parseInt(currentService.service_duration_m) + ' м.';
                    }
                    var start = new Date(currentEvent.start);
                    var end = new Date(currentEvent.end);

                    var hoursStart = start.getHours().toString().padStart(2, "0"),
                        minutesStart = start.getMinutes().toString().padStart(2, "0"),
                        hoursEnd = end.getHours().toString().padStart(2, "0"),
                        minutesEnd = end.getMinutes().toString().padStart(2, "0");

                    serviceDuration += ' (';
                    serviceDuration += hoursStart + ':' + minutesStart;
                    serviceDuration += ' - ';
                    serviceDuration += hoursEnd + ':' + minutesEnd;
                    serviceDuration += ')';

                    $('.service-duration-summary').find('small').html(serviceDuration);
                }
            }
        },
        initEvents: function (target) {
            $('.btn-delete', target).on('click', this.deleteService.bind(this));
            $('.btn-qty-plus', target).on('click', this.increaseServiceQty.bind(this));
            $('.btn-qty-minus', target).on('click', this.decreaseServiceQty.bind(this));
            $('.action-select.admin__action-multiselect', target).on('click', this.initServicesDropdown.bind(this));
            $('.action-select.admin__action-multiselect', target).on('focusin', this.onFocusIn.bind(this));
            $('.action-select.admin__action-multiselect', target).on('focusout', this.onFocusOut.bind(this));
            $('.service-details', target).on('click', this.selectService.bind(this));
            // this.initAutocomplete(target);
            /*$(this.eventTableContainer).find(".fieldset-wrapper").collapsible({
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

        isSelected: function (value) {
            return this.value() === value;
        },

        toggleListVisible: function () {
            this.listVisible(!this.listVisible());

            return this;
        },

        outerClick: function () {
            this.listVisible() ? this.listVisible(false) : false;
        },

        onFocusIn: function (ctx, event) {
            this.multiselectFocus(true);
        },

        onFocusOut: function (ctx, event) {
            this.multiselectFocus(false);
        }
    });
});