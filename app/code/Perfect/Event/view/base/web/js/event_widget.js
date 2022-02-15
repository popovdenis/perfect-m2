define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'timetableAppointmentService',
    'Perfect_Event/js/model/md5',
    'underscore',
    'Perfect_Event/js/storage',
    'Perfect_Event/js/event',
    'Magento_Ui/js/modal/confirm',
    'eventCalendarLib',
    'qcTimepicker',
    'spectrum',
    'mage/calendar',
    'jquery/ui',
    'domReady!'
], function ($, modal, timetableAppointment, md5, _, storage, Event, confirm) {
    'use strict';

    $.widget('perfect.event',{
        options: {
            schedulerId: null,
            scheduler: null,
            appointments: [],
            appointmentModal: null,
            appointmentForm: '.form.appointment',
            searchConfig: {},
            employeeHash: {},
            employeesHash: {}
        },
        lastAppointmentId: null,
        eventCalendarObject: null,
        isPopupActive: false,
        popupObject: null,

        /**
         * Initialize widget
         */
        _create: function() {
            this.initPopup();
            this._initScheduler();
            this.initEvents();
            this._initAutocomplete();
        },

        initPopup: function () {
            var self = this;

            self.popupObject = modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: '',
                clickableOverlay: true,
                buttons: [{
                    text: $.mage.__('Delete'),
                    class: 'action secondary delete-appointment',
                    click: function (event) {
                        confirm({
                            title: $.mage.__('Delete appointment'),
                            content: $.mage.__('Are you sure you want to delete this appointment?'),
                            actions: {
                                confirm: function () {
                                    self._deleteAppointment(storage.currentEvent());
                                },
                                cancel: function () {
                                    return false;
                                },
                                always: function () {
                                    self.popupObject.closeModal();
                                }
                            }
                        });
                    }
                }, {
                    text: $.mage.__('Save'),
                    class: 'action primary save-appointment',
                    click: function (event) {
                        $(self.options.appointmentForm).trigger('submit');
                    }
                }]
            }, $(this.options.appointmentModal));

            $(this.options.appointmentModal).on('modalclosed', function() {
                self.isPopupActive = false;
            });
        },

        /**
         * This method binds elements.
         * @private
         */
        _initScheduler: function() {
            var self = this,
                scheduler = document.getElementById(self.options.scheduler),
                options = {
                    view: 'timeGridDay',
                    height: '100%',
                    headerToolbar: {
                        start: 'prev,today,next',
                        center: 'title',
                        end: 'timeGridDay,timeGridWeek,dayGridMonth'
                    },
                    buttonText: function (texts) {
                        texts.today = 'Сегодня';
                        texts.dayGridMonth = 'месяц';
                        texts.resourceTimeGridDay = 'день';
                        texts.timeGridDay = 'день';
                        texts.resourceTimeGridWeek = 'неделя';
                        texts.timeGridWeek = 'неделя';
                        return texts;
                    },
                    scrollTime: '08:00:00',
                    slotMinTime: '08:00:00',
                    slotMaxTime: '21:00:00',
                    dayMaxEvents: true,
                    nowIndicator: true,
                    events: self._populateAppointments(),
                    views: {
                        timeGridDay: {pointer: true},
                        resourceTimeGridDay: {pointer: true},
                        timeGridWeek: {pointer: true},
                        resourceTimeGridWeek: {pointer: true}
                    },
                    dateClick: function (dateClickInfo) {
                        if (!self.isPopupActive) {
                            self.isPopupActive = true;
                            storage.currentEvent(Event.newEvent(dateClickInfo));
                            self.populatePopup(storage.currentEvent());
                            self.openPopup();
                        }
                    },
                    eventClick: function (eventClickInfo) {
                        self.isPopupActive = true;
                        storage.currentEvent(eventClickInfo.event);
                        self.populatePopup(eventClickInfo.event);
                        self.openPopup();
                    },
                    eventDrop: function (eventClickInfo) {
                        storage.currentEvent(eventClickInfo.event);
                        self._saveAppointment(eventClickInfo.event);
                    },
                    eventContent: function (eventInfo) {
                        if (eventInfo.event.id === '{pointer}') {
                            return '<div class="ec-event-time" style="background-color:#B29DD9;width:100%">' + eventInfo.timeText + ' Новая запись' + '</div>';
                        }

                        return '<div class="ec-event-time">' + eventInfo.timeText + '</div>' +
                        '<div class="ec-event-title">' + eventInfo.event.title + '</div>';
                    },
                    datesSet: function (info) {
                    }
                };

            return new Promise(function() {
                self.eventCalendarObject = new EventCalendar(scheduler, options);
                storage.eventCalendars().push({
                    employeeHash: self.options.employeeHash,
                    calendar: self.eventCalendarObject
                });
                self.options.employeesHash[self.options.employeeHash] = self.eventCalendarObject;
            });
        },

        _populateAppointments: function () {
            var appointments = [];

            for (var entityId in this.options.appointments) {
                var appointment = this.options.appointments[entityId],
                    startedAt = this._convertDateToEventFormat(appointment.started_at),
                    color = _.isEmpty(appointment.appointment_color)
                        ? "#fe7b62"
                        : this.rgbToHex(appointment.appointment_color);

                appointments.push({
                    id: appointment.id,
                    start: this._convertDateToEventFormat(appointment.started_at),
                    end: this._convertDateToEventFormat(appointment.finished_at),
                    title: appointment.service_name,
                    color: color,
                    resourceId: 2,
                    extendedProps: {
                        employee_id: appointment.employee_id,
                        employeeHash: md5().hash(appointment.employee_id),
                        client: appointment.client
                    }
                });
            }

            return appointments;
        },

        _saveAppointment: function (appointment) {
            var self = this;
            timetableAppointment.sendAppointment(appointment)
                .then(function (appointmentData) {
                    var event = storage.currentEvent(),
                        isEventNew = false;

                    if (_.isEmpty(event.id)) {
                        isEventNew = true;
                        event = $.extend(event, appointmentData);
                    }
                    var eventEmployeeHash = event.extendedProps.employeeHash,
                        calendar = storage.searchEventCalendar(eventEmployeeHash),
                        appointmentEmployeeHash = md5().hash(appointmentData.employee_id);

                    if (typeof event !== "undefined" && !_.isEmpty(event) && Number.isInteger(parseInt(event.id))) {
                        if (!_.isEmpty(appointmentData.service_name)) {
                            event.title = appointmentData.service_name;
                        }
                        if (!_.isEmpty(appointmentData.started_at)) {
                            event.start = self._convertDateToEventFormat(appointmentData.started_at);
                        }
                        if (!_.isEmpty(appointmentData.finished_at)) {
                            event.end = self._convertDateToEventFormat(appointmentData.finished_at);
                        }
                        if (!_.isEmpty(appointmentData.appointment_color)) {
                            event.backgroundColor = self.rgbToHex(appointmentData.appointment_color);
                            event.extendedProps.appointment_color = event.backgroundColor;
                        }
                        if (!_.isEmpty(appointmentData.employee_id)) {
                            event.extendedProps.employee_id = appointmentData.employee_id;
                            event.extendedProps.employeeHash = md5().hash(appointmentData.employee_id);
                        }

                        if (eventEmployeeHash === appointmentEmployeeHash) {
                            calendar.updateEvent(event);
                        } else {
                            if (!isEventNew) {
                                calendar.removeEventById(event.id);
                            }
                            calendar = storage.searchEventCalendar(appointmentEmployeeHash);
                            calendar.addEvent(event);
                        }
                    }
                });
        },

        _deleteAppointment: function (appointment) {
            var self = this;
            timetableAppointment.deleteAppointment(appointment)
                .then(function () {
                    if (!_.isEmpty(appointment.extendedProps.employeeHash)) {
                        var calendar = storage.searchEventCalendar(appointment.extendedProps.employeeHash);
                        if (!_.isEmpty(calendar)) {
                            calendar.removeEventById(appointment.id);
                        }
                    }
                });
        },

        _convertDateToEventFormat: function (datetime) {
            var regex = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/,
                datetimeFormatted;

            datetimeFormatted = new Date(datetime.replace(regex, '$1-$2-$3'));

            datetimeFormatted.setHours(
                datetime.replace(regex, '$4'),
                datetime.replace(regex, '$5')
            );

            return datetimeFormatted;
        },

        rgbToHex: function (rgb) {
            let sep = rgb.indexOf(",") > -1 ? "," : " ";
            // Turn "rgb(r,g,b)" into [r,g,b]
            rgb = rgb.substr(4).split(")")[0].split(sep);

            let r = (+rgb[0]).toString(16),
                g = (+rgb[1]).toString(16),
                b = (+rgb[2]).toString(16);

            if (r.length == 1)
                r = "0" + r;
            if (g.length == 1)
                g = "0" + g;
            if (b.length == 1)
                b = "0" + b;

            return "#" + r + g + b;
        },

        initEvents: function () {
            let self = this,
                appointmentForm = $('.form.appointment');

            $(appointmentForm).off('submit').on('submit', function(e) {
                e.preventDefault();
                // if ($(appointmentForm).valid()) {
                    var formData = {};
                    for (const field of $(appointmentForm).serializeArray()) {
                        formData[field.name] = field.value;
                    }
                    self._saveAppointment(formData);
                    self.popupObject.closeModal();
                // }
            });

            $('#appointment_time_start').qcTimepicker({classes: 'admin__control-select'});
            $('#appointment_time_end').qcTimepicker({classes: 'admin__control-select'});
            $('#appointment_color').spectrum(this.getSpectrumOptions('blanchedalmond'));
            $("#appointment_date").calendar({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                showMonthAfterYear: false,
                showButtonPanel: true
            });
        },

        populatePopup: function (appointment) {
            // appointment
            $('input[name="id"]').val(appointment.id);

            var start = new Date(appointment.start);
            $('#appointment_date').val(start.toLocaleDateString("en-GB"));
            $('#appointment_time_start-qcTimepicker option[value="' + start.toLocaleTimeString("en-GB") + '"]').prop('selected', true).trigger('change');
            var end = new Date(appointment.end);
            $('#appointment_time_end-qcTimepicker option[value="' + end.toLocaleTimeString("en-GB") + '"]').prop('selected', true).trigger('change');
            $('#appointment_color').spectrum(this.getSpectrumOptions(appointment.backgroundColor));

            // client
            $('input[name="client_id"]').val(appointment.extendedProps.client.client_id);
            $('input[name="client_name"]').val(appointment.extendedProps.client.client_name);
            $('input[name="client_phone"]').val(appointment.extendedProps.client.client_phone);
            $('input[name="client_email"]').val(appointment.extendedProps.client.client_email);

            // services
            $('input[name="service_name"]').val(appointment.title);

            // master
            $('#employee_id option[value="' + appointment.extendedProps.employee_id + '"]').prop('selected', true);
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
        },

        openPopup: function () {
            $(this.options.appointmentModal).modal("openModal");
        },

        _initAutocomplete: function () {
            var self = this;
            $('input[name="client_name"]').autocomplete({
                minLength: 2,
                source: function(request, response) {
                    $.ajax( {
                        url: self.options.searchConfig.url,
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
                    $('input[name="client_id"]').val(ui.item.client_id);
                    $('input[name="client_name"]').val(ui.item.client_name);
                    $('input[name="client_phone"]').val(ui.item.client_phone);
                    $('input[name="client_email"]').val(ui.item.client_email);
                }
            });
        }
    });

    return $.perfect.event;
});