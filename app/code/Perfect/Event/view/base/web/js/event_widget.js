define([
    'jquery',
    'Perfect_Event/js/storage',
    'Perfect_Event/js/event',
    'Perfect_Event/js/model/appointmentManager',
    'Perfect_Event/js/appointment-popup',
    'eventCalendarLib',
    'domReady!'
], function ($, storage, Event, appointmentManager, appointmentPopup) {
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
                    events: appointmentManager().populateAppointments(this.options.appointments),
                    views: {
                        timeGridDay: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}, locale: 'ru'},
                        timeGridWeek: {pointer: true, titleFormat: {year: 'numeric', month: '2-digit', day: 'numeric'}, locale: 'ru'},
                        dayGridMonth: {pointer: true, titleFormat: {year: 'numeric', month: 'short', day: 'numeric'}, locale: 'ru'},
                        resourceTimeGridDay: {pointer: true},
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
                        appointmentManager().saveAppointment(eventClickInfo.event);
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
                appointmentForm = $('.form.appointment');

            $(appointmentForm).off('submit').on('submit', function(e) {
                e.preventDefault();
                // if ($(appointmentForm).valid()) {
                    var formData = {};
                    for (const field of $(appointmentForm).serializeArray()) {
                        formData[field.name] = field.value;
                    }
                appointmentManager().saveAppointment(formData);
                appointmentPopup().closePopup();
                // }
            });
        }
    });

    return $.perfect.event;
});