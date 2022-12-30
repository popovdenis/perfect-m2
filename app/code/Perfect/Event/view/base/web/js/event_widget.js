define([
    'jquery',
    'Perfect_Event/js/storage',
    'Perfect_Event/js/event',
    'Perfect_Event/js/model/eventManager',
    'Perfect_Event/js/event-popup',
    'eventCalendarLib',
    'domReady!'
], function ($, storage, Event, eventManager, eventPopup) {
    'use strict';

    $.widget('perfect.event',{
        options: {
            schedulerId: null,
            scheduler: null,
            events: [],
            eventForm: '.form.event',
            searchConfig: {},
            employeeHash: {},
            employeesHash: {}
        },
        lastEventId: null,
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
                    events: eventManager().populateEvents(this.options.events),
                    master_id: null,
                    views: {
                        timeGridDay: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}, locale: 'ru'},
                        timeGridWeek: {pointer: true, titleFormat: {year: 'numeric', month: '2-digit', day: 'numeric'}, locale: 'ru'},
                        dayGridMonth: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}, locale: 'ru'},
                        resourceTimeGridDay: {pointer: true},
                        resourceTimeGridWeek: {pointer: true}
                    },
                    dateClick: function (dateClickInfo) {
                        if (!eventPopup().isPopupActive()) {
                            let dateInfo = Event.newEvent(dateClickInfo);
                            dateInfo.extendedProps.employee_id = self.options.master_id;
                            eventPopup().preparePopup(dateInfo).openPopup();
                        }
                    },
                    eventClick: function (eventClickInfo) {
                        eventPopup().preparePopup(eventClickInfo.event).openPopup();
                    },
                    eventDrop: function (eventClickInfo) {
                        storage.currentEvent(eventClickInfo.event);
                        eventManager().saveEvent(eventClickInfo.event);
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

        initEvents: function () {
            let self = this,
                eventForm = $('.form.event');

            $(eventForm).off('submit').on('submit', function(e) {
                e.preventDefault();
                // if ($(eventForm).valid()) {
                //     var formData = {};
                //     for (const field of $(eventForm).serialize()) {
                //         formData[field.name] = field.value;
                //     }
                eventManager().saveEvent($(eventForm).serialize());
                eventPopup().closePopup();
                // }
            });
        },

        serialize: function (form) {
            // Setup our serialized data
            var serialized = [];
            var formData = {};

            // Loop through each field in the form
            for (var i = 0; i < form.elements.length; i++) {

                var field = form.elements[i];

                // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
                if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

                // If a multi-select, get all selections
                if (field.type === 'select-multiple') {
                    for (var n = 0; n < field.options.length; n++) {
                        if (!field.options[n].selected) continue;
                        serialized.push({
                            name: field.name,
                            value: field.value
                        });
                    }
                }
                // Convert field data to a query string
                else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                    serialized.push({
                        name: field.name,
                        value: field.value
                    });
                }
            }

            return serialized;

        }
    });

    return $.perfect.event;
});