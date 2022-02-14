define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'timetableAppointmentService',
    'Perfect_Event/js/model/md5',
    'underscore',
    'eventCalendarLib',
    'qcTimepicker',
    'spectrum',
    'jquery/ui',
    'domReady!'
], function ($, modal, timetableAppointment, CryptoJS, _) {
    'use strict';

    $.widget('perfect.event',{
        options: {
            schedulerId: null,
            scheduler: null,
            appointments: [],
            appointmentModal: null,
            appointmentForm: '.form.appointment',
            searchConfig: {}
        },
        lastAppointmentId: null,
        eventCalendarObject: null,
        eventCalendarObjects: [],
        appointmentSlots: [],
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
                    text: $.mage.__('Save'),
                    class: 'action primary',
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
                            var hash = CryptoJS().hash((new Date(dateClickInfo.date)).toLocaleString());
                            if (!self.appointmentSlots.includes(hash)) {
                                console.log(dateClickInfo);
                                self.isPopupActive = true;

                                var startedAt = new Date(dateClickInfo.date);
                                self.populatePopup({
                                    id: '',
                                    title: '',
                                    appointment_date: startedAt.getDate() + '/' + startedAt.getMonth() + '/' + startedAt.getFullYear(),
                                    extendedProps: {
                                        employee_id: null,
                                        client: {
                                            client_id: null,
                                            client_name: null,
                                            client_phone: null,
                                            client_email: null
                                        }
                                    }
                                });
                                self.openPopup();
                            }
                        }
                    },
                    eventClick: function (eventClickInfo) {
                        self.isPopupActive = true;
                        self.populatePopup(eventClickInfo.event);
                        self.openPopup();
                    },
                    eventDrop: function (eventClickInfo) {
                        var hash = CryptoJS().hash((new Date(eventClickInfo.oldEvent.start)).toLocaleString());
                        if (self.appointmentSlots.includes(hash)) {
                            self.appointmentSlots.splice(self.appointmentSlots.indexOf(hash), 1);

                            hash = CryptoJS().hash((new Date(eventClickInfo.event.start)).toLocaleString());
                            self.appointmentSlots.push(hash);

                            self._saveAppointment(eventClickInfo.event);
                        }
                    },
                    eventContent: function (eventInfo) {
                        if (eventInfo.event.id === '{pointer}') {
                            return '<div class="ec-event-time" style="background-color:#B29DD9;width:100%">' + eventInfo.timeText + ' Новая запись' + '</div>';
                        }

                        return '<div class="ec-event-time">' + eventInfo.timeText + '</div>' +
                        '<div class="ec-event-title">' + eventInfo.event.title + '</div>';
                    },
                    datesSet: function (info) {
                        console.log(info);
                    }
                };

            return new Promise(function() {
                self.eventCalendarObject = new EventCalendar(scheduler, options);
                self.eventCalendarObjects.push(self.eventCalendarObject);
            });
        },

        _populateAppointments: function () {
            var appointments = [];

            for (var entityId in this.options.appointments) {
                var appointment = this.options.appointments[entityId],
                    startedAt = this._convertDateToEventFormat(appointment.started_at),
                    color = _.isEmpty(appointment.appointment_color)
                        ? "#FE6B64"
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
                        client: appointment.client
                    }
                });

                this.appointmentSlots.push(
                    CryptoJS().hash(startedAt.toLocaleString())
                );
            }

            return appointments;
        },

        createEvents: function () {
            let days = [];
            for (let i = 0; i < 7; ++i) {
                let day = new Date();
                let diff = i - day.getDay();
                day.setDate(day.getDate() + diff);
                days[i] = day.getFullYear() + "-" + day.getMonth()+1 + "-" + day.getDate();
            }

            return [
                {start: days[0] + " 00:00", end: days[0] + " 09:00", resourceId: 1, display: "background"},
                {start: days[1] + " 12:00", end: days[1] + " 14:00", resourceId: 2, display: "background"},
                {start: days[2] + " 17:00", end: days[2] + " 24:00", resourceId: 1, display: "background"},
                {start: days[0] + " 10:00", end: days[0] + " 14:00", resourceId: 1, title: "The calendar can display background and regular events", color: "#FE6B64"},
                {start: days[1] + " 16:00", end: days[2] + " 08:00", resourceId: 2, title: "An event may span to another day", color: "#B29DD9"},
                {start: days[2] + " 09:00", end: days[2] + " 13:00", resourceId: 2, title: "Events can be assigned to resources and the calendar has the resources view built-in", color: "#779ECB"},
                {start: days[3] + " 14:00", end: days[3] + " 20:00", resourceId: 1, title: "", color: "#FE6B64"},
                {start: days[3] + " 15:00", end: days[3] + " 18:00", resourceId: 1, title: "Overlapping events are positioned properly", color: "#779ECB"},
                {start: days[5] + " 10:00", end: days[5] + " 16:00", resourceId: 2, title: "You have complete control over the <i><b>display</b></i> of events…", color: "#779ECB"},
                {start: days[5] + " 14:00", end: days[5] + " 19:00", resourceId: 2, title: "…and you can drag and drop the events!", color: "#FE6B64"},
                {start: days[5] + " 18:00", end: days[5] + " 21:00", resourceId: 2, title: "", color: "#B29DD9"}
            ];
        },

        createEvent: function (data, object) {
            var startedAt = new Date(data.date),
                finishedAt = new Date(data.date);

            finishedAt.setHours(startedAt.getHours() + 1);

            this.eventCalendarObject.addEvent({
                start: startedAt,
                end:  finishedAt,
                resourceId: 2,
                display: "auto",
                title: "Стрижка",
                color: "#779ECB"
            });
        },

        _saveAppointment: function (appointment) {
            var self = this;
            timetableAppointment.sendAppointment(appointment)
                .then(function (appointmentData) {
                    for (const calendar of self.eventCalendarObjects) {
                         var event = calendar.getEventById(appointmentData.id);
                         if (typeof event !== "undefined" && Number.isInteger(parseInt(event.id))) {
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
                             }

                             calendar.updateEvent(event);
                             break;
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
            $('#appointment-color').spectrum({
                showPaletteOnly: true,
                showPalette: true,
                color: 'blanchedalmond',
                palette: [
                    ['black', 'white', 'blanchedalmond', 'rgb(255, 128, 0);', 'hsv 100 70 50'],
                    ['red', 'yellow', 'green', 'blue', 'violet']
                ]
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