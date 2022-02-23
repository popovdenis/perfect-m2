define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    var currentEvent = ko.observable(),
        eventCalendars = ko.observableArray(),
        masterServices = ko.observableArray();

    return {
        currentEvent: currentEvent,
        eventCalendars: eventCalendars,
        masterServices: masterServices,

        searchEventCalendar: function (hash) {
            var calendarObject = eventCalendars().find(o => o.employeeHash === hash);

            return calendarObject ? calendarObject.calendar : {};
        },
        getMasterServices: function (masterId) {
            var services = this.masterServices().find(o => o.masterId === masterId);

            return services ? services.services : {};
        },
        getServiceById: function (masterId, serviceId) {
            var service = this.getMasterServices(masterId).find(o => parseInt(o.service_id) === serviceId);

            return service ? service : {};
        }
    };
});