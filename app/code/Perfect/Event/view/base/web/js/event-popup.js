define([
    'jquery',
    'underscore',
    'ko',
    'uiElement',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    'Perfect_Event/js/storage',
    'spectrum',
    'qcTimepicker',
    'mage/calendar',
    'mage/collapsible',
    'jquery/ui',
], function ($, _, ko, Component, modal, confirm, storage) {
    'use strict';

    return Component.extend({
        defaults: {
            initializedPopup: ko.observable(false),
            event: ko.observableArray(),
            isPopupActive: ko.observable(false),
            eventModal: '.event-modal',
            eventForm: '.form.event',
            listens: {
                event: 'initEventListener',
                initializedPopup: 'init',
            }
        },
        init: function () {
            this.initPopup();
            this.initEvents();
        },
        initPopup: function () {
            var self = this,
                popupObject;

            popupObject = modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: '',
                clickableOverlay: true,
                buttons: [{
                    text: $.mage.__('Delete'),
                    class: 'action secondary delete-event',
                    click: function (event) {
                        confirm({
                            title: $.mage.__('Delete event'),
                            content: $.mage.__('Are you sure you want to delete this event?'),
                            actions: {
                                confirm: function () {
                                    self._deleteEvent(storage.currentEvent());
                                },
                                cancel: function () {
                                    return false;
                                },
                                always: function () {
                                    popupObject.closeModal();
                                }
                            }
                        });
                    }
                }, {
                    text: $.mage.__('Save'),
                    class: 'action primary save-event',
                    click: function (event) {
                        $(self.eventForm).trigger('submit');
                    }
                }]
            }, $(this.eventModal));

            $(this.eventModal).on('modalclosed', function() {
                self.isPopupActive(false);
            });
        },
        initEvents: function () {
            $('#event_time_start').qcTimepicker({classes: 'admin__control-select'});
            $('#event_time_end').qcTimepicker({classes: 'admin__control-select'});
            $("#event_date").calendar({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                showMonthAfterYear: false,
                showButtonPanel: true
            });
            $(".fieldset-wrapper").collapsible({
                "header": ".fieldset-wrapper-title",
                "content": ".admin__fieldset-wrapper-content",
                "openedState": "_show",
                "closedState": "_hide",
                "active": true
            });
        },
        initEventListener: function (event) {
            storage.currentEvent(event);
        },
        preparePopup: function (event) {
            if (!this.initializedPopup()) {
                this.initializedPopup(true);
            }
            this.event(event);

            // event
            $('input[name="id"]').val(event.id);

            var start = new Date(event.start);
            $('#event_date').val(start.toLocaleDateString("en-GB"));
            $('#event_time_start-qcTimepicker option[value="' + start.toLocaleTimeString("en-GB") + '"]').prop('selected', true).trigger('change');
            var end = new Date(event.end);
            $('#event_time_end-qcTimepicker option[value="' + end.toLocaleTimeString("en-GB") + '"]').prop('selected', true).trigger('change');
            $('#event_color').spectrum(this.getSpectrumOptions(event.backgroundColor));

            // client
            $('input[name="client_id"]').val(event.extendedProps.client.client_id);
            $('input[name="client_name"]').val(event.extendedProps.client.client_name);
            $('input[name="client_phone"]').val(event.extendedProps.client.client_phone);

            // services
            $('input[name="service_name"]').val(event.title);

            // master
            $('#employee_id option[value="' + event.extendedProps.employee_id + '"]').prop('selected', true);

            return this;
        },
        openPopup: function () {
            this.isPopupActive(true);
            $(this.eventModal).modal("openModal");
        },
        closePopup: function () {
            $(this.eventModal).modal("closeModal");
        },
        getSpectrumOptions: function (color) {
            return {
                showPaletteOnly: true,
                showPalette: true,
                color: color,
                palette: [
                    ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                    ["#181818","#d4d101","#646177","#8c9999", "#b0cbcc","#dfeee8","#e0f3d9","#fff"],
                    ["#f00","#f90","#ff0","#00c400","#0ff","#00f","#90f","#f0f"],
                    ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                    ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                    ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                    ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                    ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                    ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                ]
            }
        }
    })
});