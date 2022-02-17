define([
    'jquery',
    'timetableAppointmentService',
    'Perfect_Event/js/model/md5',
    'underscore',
    'Perfect_Event/js/storage',
    'Perfect_Event/js/event',
    'Perfect_Event/js/appointment-popup',
    'eventCalendarLib',
    'domReady!'
], function ($, timetableAppointment, md5, _, storage, Event, appointmentPopup) {
    'use strict';

    $.widget('perfect.event',{
        options: {
            schedulerId: null,
            scheduler: null,
            appointments: [],
            appointmentForm: '.form.appointment',
            searchConfig: {},
            employeeHash: {},
            employeesHash: {}
        },
        lastAppointmentId: null,
        eventCalendarObject: null,

        /**
         * Initialize widget
         */
        _create: function() {
            this._initScheduler();
            this.initEvents();
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
                        timeGridDay: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}},
                        resourceTimeGridDay: {pointer: true},
                        timeGridWeek: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}},
                        dayGridMonth: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}},
                        resourceTimeGridWeek: {pointer: true}
                    },
                    dateClick: function (dateClickInfo) {
                        if (!appointmentPopup().isPopupActive()) {
                            appointmentPopup().preparePopup(Event.newEvent(dateClickInfo)).openPopup();
                        }
                    },
                    eventClick: function (eventClickInfo) {
                        appointmentPopup().preparePopup(eventClickInfo.event).openPopup();
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
                appointmentPopup().closePopup();
                // }
            });
        }
    });

    return $.perfect.event;
});