define([
    'uiClass',
    'jquery',
    'Perfect_Event/js/model/timetable-event-service',
    'Perfect_Event/js/model/md5',
    'underscore',
    'Perfect_Event/js/storage'
], function (Class, $, timetableEvent, md5, _, storage) {
    'use strict';

    return Class.extend({
        populateEvents: function (eventsList) {
            var events = [];

            for (var entityId in eventsList) {
                var event = eventsList[entityId],
                    color = _.isEmpty(event.event_color)
                        ? "#fe7b62"
                        : this.rgbToHex(event.event_color);

                events.push({
                    id: event.id,
                    start: this.convertDateToEventFormat(event.started_at),
                    end: this.convertDateToEventFormat(event.finished_at),
                    title: event.service_name,
                    color: color,
                    resourceId: 2,
                    extendedProps: {
                        employee_id: event.employee_id,
                        employeeHash: md5().hash(event.employee_id),
                        client: event.client
                    }
                });
            }

            return events;
        },

        saveEvent: function (event) {
            var self = this;
            timetableEvent.sendEvent(event)
                .then(function (eventData) {
                    var event = storage.currentEvent(),
                        isEventNew = false;

                    if (_.isEmpty(event.id)) {
                        isEventNew = true;
                        event = $.extend(event, eventData);
                    }
                    var eventEmployeeHash = event.extendedProps.employeeHash,
                        calendar = storage.searchEventCalendar(eventEmployeeHash),
                        eventEmployeeHash = md5().hash(eventData.employee_id);

                    if (typeof event !== "undefined" && !_.isEmpty(event) && Number.isInteger(parseInt(event.id))) {
                        if (!_.isEmpty(eventData.service_name)) {
                            event.title = eventData.service_name;
                        }
                        if (!_.isEmpty(eventData.started_at)) {
                            event.start = self.convertDateToEventFormat(eventData.started_at);
                        }
                        if (!_.isEmpty(eventData.finished_at)) {
                            event.end = self.convertDateToEventFormat(eventData.finished_at);
                        }
                        if (!_.isEmpty(eventData.event_color)) {
                            event.backgroundColor = self.rgbToHex(eventData.event_color);
                            event.extendedProps.event_color = event.backgroundColor;
                        }
                        if (!_.isEmpty(eventData.employee_id)) {
                            event.extendedProps.employee_id = eventData.employee_id;
                            event.extendedProps.employeeHash = md5().hash(eventData.employee_id);
                        }

                        if (eventEmployeeHash === eventEmployeeHash) {
                            calendar.updateEvent(event);
                        } else {
                            if (!isEventNew) {
                                calendar.removeEventById(event.id);
                            }
                            calendar = storage.searchEventCalendar(eventEmployeeHash);
                            calendar.addEvent(event);
                        }
                    }
                });
        },

        deleteEvent: function (event) {
            var self = this;
            timetableEvent.deleteEvent(event)
                .then(function () {
                    if (!_.isEmpty(event.extendedProps.employeeHash)) {
                        var calendar = storage.searchEventCalendar(event.extendedProps.employeeHash);
                        if (!_.isEmpty(calendar)) {
                            calendar.removeEventById(event.id);
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