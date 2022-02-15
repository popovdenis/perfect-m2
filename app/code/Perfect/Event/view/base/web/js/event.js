define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    var Event = function() {
        var self = this;
        self.id = null;
        self.resourceIds = 2;
        self.start = null;
        self.end = null;
        self.title = null;
        self.startEditable = null;
        self.display = "auto";
        self.extendedProps = {
            employee_id: null,
            client: {
                client_id: null,
                client_name: null,
                client_phone: null,
                client_email: null
            }
        };
    };

    return {
        newEvent: function (eventData) {
            var event = new Event();

            event.start = new Date(eventData.date);
            event.end = new Date(eventData.date);
            event.end.setHours(event.end.getHours() + 1);

            return event;
        },
    };
});