define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    var currentEvent = ko.observable(),
        eventCalendars = ko.observableArray();

    return {
        currentEvent: currentEvent,
        eventCalendars: eventCalendars,

        searchEventCalendar: function (hash) {
            var calendarObject = eventCalendars().find(o => o.employeeHash === hash);

            return calendarObject ? calendarObject.calendar : {};
        }
    };
});