define([
    'jquery',
    'uiElement',
    'mage/template',
    'Perfect_Service/js/view/template/default',
    'Perfect_Service/js/model/serviceManager',
    'Perfect_Event/js/model/config',
    'Perfect_Event/js/storage',
    'ko',
    'underscore'
], function($, Component, mageTemplate, defaultTemplate, serviceManager, config, storage, ko, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perfect_Service/service_new',
            eventTableContainer: '.event-service-table tbody',
            listVisible: ko.observable(false),
            eventDurationSummary: ko.observable(0),
            inputValue: '',
            multiselectFocus: false,
            serviceRowClass: '.data-row',
            serviceItemClass: '.service-item',
            serviceItemIndex: 'service-index',
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

        addServiceRow: function () {
            var params = {data: {row_index: $(this.eventTableContainer).find(this.serviceRowClass).length}};
            var newServiceTemplate = $(mageTemplate(defaultTemplate())(params));

            $(this.eventTableContainer).append(newServiceTemplate);

            var self = this;
            serviceManager().getServices(storage.currentEvent().extendedProps.employee_id, function (services) {
                var servicesSearchItems = newServiceTemplate.find('.services-search-items');

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

                        let dropdown =
                            '<li class="admin__action-multiselect-menu-inner-item service-item" data-role="option-group" data-service-index="' + service.service_id + '">\n' +
                                '<div class="action-menu-item service-details">\n' +
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

                self.initEvents(newServiceTemplate);
            });

            return this;
        },

        deleteService: function (event) {
            var self = this,
                target = $(event.target).closest('.data-row');

            var serviceIndex = target.find('.selected-service-name').data('service-index');
            if (serviceIndex) {
                self.deleteEventService(storage.currentEvent(), serviceIndex);
            }

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
            var rowElement = $(event.currentTarget).closest(this.serviceRowClass),
                serviceId = rowElement.data(this.serviceItemIndex),
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

                if (parseInt(serviceId) && typeof serviceId !== "undefined") {
                    var service = this.getCurrentEventServiceById(serviceId);
                    if (!_.isEmpty(service)) {
                        service.service_quantity = qty;
                    }
                    this.updateEventPriceSummary();
                }
            }

            var minusElement = rowElement.find('.btn-qty-minus');
            if (minusElement.prop('disabled')) {
                minusElement.prop('disabled', false);
            }
        },

        decreaseServiceQty: function (event) {
            var rowElement = $(event.currentTarget).closest(this.serviceRowClass),
                serviceId = rowElement.data(this.serviceItemIndex),
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

                if (parseInt(serviceId) && typeof serviceId !== "undefined") {
                    var service = this.getCurrentEventServiceById(serviceId);
                    if (!_.isEmpty(service)) {
                        service.service_quantity = qty;
                    }
                    this.updateEventPriceSummary();
                }
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

        selectServiceFromList: function (event) {
            var rowElement = $(event.currentTarget).closest(this.serviceItemClass),
                serviceId = rowElement.data(this.serviceItemIndex);

            if (parseInt(serviceId) && typeof serviceId !== "undefined") {
                var currentEvent = storage.currentEvent(),
                    currentService = storage.getServiceById(currentEvent.extendedProps.employee_id, serviceId);

                if (currentService && typeof currentService.service_name !== "undefined") {
                    $('.selected-service-name', $(event.currentTarget).closest('.admin__field-control'))
                        .attr('data-service-index', currentService.service_id)
                        .text(currentService.service_name);

                    $(event.currentTarget).closest(this.serviceRowClass)
                        .attr('data-' + this.serviceItemIndex, serviceId);

                    var inputQty = $(event.currentTarget).closest(this.serviceRowClass).find('.input-qty');
                    if (inputQty) {
                        currentService.service_quantity = 0;
                        if (_.isNumber(parseInt(inputQty.val()))) {
                            currentService.service_quantity = parseInt(inputQty.val());
                        }
                    }

                    this.addCurrentEventService(currentService);
                    this.updateEventPriceSummary();
                }
            }
        },

        updateEventPriceSummary: function () {
            var currentEvent = storage.currentEvent(),
                services = currentEvent.services();

            currentEvent.eventPriceSummary = ko.observable(0);
            currentEvent.eventPriceSummaryRange = ko.observable(0);

            for (const index in services) {
                if (services.hasOwnProperty(index)) {
                    let service = services[index],
                        priceFrom = parseInt(service.service_price_from);

                    currentEvent.eventPriceSummary(
                        currentEvent.eventPriceSummary() + priceFrom * parseInt(service.service_quantity)
                    );

                    if (!_.isEmpty(service.is_price_range)) {
                        var priceTo = parseInt(service.service_price_to);

                        currentEvent.eventPriceSummaryRange(
                            currentEvent.eventPriceSummaryRange() + priceTo * parseInt(service.service_quantity)
                        );
                    }
                }
            }

            var priceSummary = currentEvent.eventPriceSummary();
            if (currentEvent.eventPriceSummaryRange()) {
                priceSummary += ' - ' + currentEvent.eventPriceSummaryRange();
            }
            $('.service-price-summary').find('small').text(priceSummary);
        },

        updateEventDurationSummary: function (currentService) {
            var currentEvent = storage.currentEvent();
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
        },

        addCurrentEventService: function (service) {
            var event = storage.currentEvent();
            if (typeof event.services === "undefined") {
                event.services = ko.observableArray();
            }
            event.services().push(service);
        },

        getCurrentEventServiceById: function (serviceId) {
            var event = storage.currentEvent();
            if (typeof event.services === "undefined") {
                return {};
            }
            var service = event.services().find(o => parseInt(o.service_id) === serviceId);

            return service ? service : {};
        },

        deleteEventService: function (event, service_id) {
            if (typeof event.services !== "undefined") {
                var serviceIndex = event.services().map(object => parseInt(object.service_id)).indexOf(service_id);
                if (serviceIndex !== -1) {
                    event.services().splice(serviceIndex, 1);
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
            $('.service-details', target).on('click', this.selectServiceFromList.bind(this));
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