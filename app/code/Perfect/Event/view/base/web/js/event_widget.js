define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'timetableAppointmentService',
    'Perfect_Event/js/lib/md5/core',
    'Perfect_Event/js/lib/md5/md5',
    'eventCalendarLib',
    'jquery/ui',
    'domReady!'
], function ($, modal, timetableAppointment, CryptoJS) {
    'use strict';

    $.widget('perfect.event',{
        options: {
            scheduler: null,
            appointments: [],
            appointmentModal: null,
            searchConfig: {}
        },
        lastAppointmentId: null,
        eventCalendarObject: null,
        appointmentSlots: [],
        activePopup: false,

        /**
         * Initialize widget
         */
        _create: function() {
            this._initScheduler();
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
                        if (!self.activePopup) {
                            var hash = CryptoJS.MD5((new Date(dateClickInfo.date)).toLocaleString()).toString();
                            if (!self.appointmentSlots.includes(hash)) {
                                console.log(dateClickInfo);
                                self.activePopup = true;
                                self._openPopup();
                            }
                        }
                    },
                    eventClick: function (eventClickInfo) {
                        self.activePopup = true;
                        self._openPopup();
                    },
                    eventDrop: function (eventClickInfo) {
                        var hash = CryptoJS.MD5((new Date(eventClickInfo.oldEvent.start)).toLocaleString()).toString();
                        if (self.appointmentSlots.includes(hash)) {
                            self.appointmentSlots.splice(self.appointmentSlots.indexOf(hash), 1);

                            hash = CryptoJS.MD5((new Date(eventClickInfo.event.start)).toLocaleString()).toString();
                            self.appointmentSlots.push(hash);

                            self._saveAppointment(eventClickInfo.event);
                        }
                    },
                    eventContent: function (eventInfo) {
                        if (eventInfo.event.id === '{pointer}') {
                            return eventInfo.timeText += ' Новая запись';
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
            });
        },

        _populateAppointments: function () {
            var appointments = [];

            for (var entityId in this.options.appointments) {
                var appointment = this.options.appointments[entityId];

                var regex = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/,
                    startedAt, finishedAt;

                startedAt = new Date(appointment.started_at.replace(regex, '$1-$2-$3'));
                finishedAt = new Date(appointment.finished_at.replace(regex, '$1-$2-$3'));

                startedAt.setHours(
                    appointment.started_at.replace(regex, '$4'),
                    appointment.started_at.replace(regex, '$5')
                );
                finishedAt.setHours(
                    appointment.finished_at.replace(regex, '$4'),
                    appointment.finished_at.replace(regex, '$5')
                );

                appointments.push({
                    id: appointment.id,
                    start: startedAt,
                    end: finishedAt,
                    title: appointment.subject,
                    color: "#FE6B64",
                    resourceId: 2
                });

                this.appointmentSlots.push(
                    CryptoJS.MD5(startedAt.toLocaleString()).toString()
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
                days[i] = day.getFullYear() + "-" + this._pad(day.getMonth()+1) + "-" + this._pad(day.getDate());
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

        _pad: function (num) {
            let norm = Math.floor(Math.abs(num));
            return (norm < 10 ? '0' : '') + norm;
        },

        _openPopup: function () {
            var self = this;

            modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: '',
                clickableOverlay: true,
                buttons: [{
                    text: $.mage.__('Save'),
                    class: 'action primary',
                    click: function (event) {
                        this.closeModal();
                        self._createAppointment(event);
                    }
                }]
            }, $(this.options.appointmentModal));

            $(this.options.appointmentModal).on('modalclosed', function() {
                self.activePopup = false;
            });

            let subscribeForm = $('.form.subscribe');

            $(subscribeForm).on('submit', function(e) {
                e.preventDefault();
                let email = $('#newsletter').val();

                if ($(subscribeForm).valid()) {
                    $.ajax({
                        url: 'newsletter/subscriber/new/',
                        type: 'POST',
                        data: {
                            'email' : email
                        },
                        dataType: 'json',
                        showLoader: true,
                        complete: function(data, status) {
                            let response = JSON.parse(data.responseText);
                            $('.newsletter-modal').text(response.message).modal('openModal');
                        }
                    });
                }
            });

            $(this.options.appointmentModal).modal("openModal");
        },

        _saveAppointment: function (appointment) {
            timetableAppointment.sendAppointment(appointment);
        },

        _createAppointment: function (event) {
            console.log(event);
        }
    });

    return $.perfect.event;
});