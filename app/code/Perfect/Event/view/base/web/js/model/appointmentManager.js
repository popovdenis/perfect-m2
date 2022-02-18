define([
    'ko',
    'uiClass',
    'jquery',
    'timetableAppointmentService',
    'Perfect_Event/js/model/md5',
    'underscore',
    'Perfect_Event/js/storage'
], function (ko, Class, $, timetableAppointment, md5, _, storage) {
    'use strict';

    return Class.extend({
        populateAppointments: function (appointmentsList) {
            var appointments = [];

            for (var entityId in appointmentsList) {
                var appointment = appointmentsList[entityId],
                    color = _.isEmpty(appointment.appointment_color)
                        ? "#fe7b62"
                        : this.rgbToHex(appointment.appointment_color);

                appointments.push({
                    id: appointment.id,
                    start: this.convertDateToEventFormat(appointment.started_at),
                    end: this.convertDateToEventFormat(appointment.finished_at),
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

        saveAppointment: function (appointment) {
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
                            event.start = self.convertDateToEventFormat(appointmentData.started_at);
                        }
                        if (!_.isEmpty(appointmentData.finished_at)) {
                            event.end = self.convertDateToEventFormat(appointmentData.finished_at);
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

        deleteAppointment: function (appointment) {
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

        convertDateToEventFormat: function (datetime) {
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
        }
    });
});