define([
    'Perfect_Event/js/model/save-event',
    'Perfect_Event/js/model/delete-event'
], function (saveEvent, deleteEvent) {
    return {
        sendEvent: function (events) {
            return new Promise(function(resolve) {
                saveEvent(events, function (eventData) {
                    resolve(eventData);
                });
            });
        },
        deleteEvent: function (event) {
            return new Promise(function(resolve) {
                deleteEvent(event, function (eventData) {
                    resolve(eventData);
                });
            });
        }
    };
});