/**
 * Widget manages tabs content loading by ajax
 */
define([
    'jquery',
    'Perfect_Event/js/jqx-all',
    'Perfect_Event/js/globalize',
    'domReady!'
], function ($) {
    'use strict';

    $.widget('perfect.timetable',{
        options: {
            scheduler: null,
            customers: []
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._initScheduler();
            this._initDateController();
        },

        _initDateController: function () {
            $('.timetable-controls-date-buttons-change-prev').off('click').on('click', function () {
                $('div[type="button"].jqx-rc-all').find('.jqx-icon-arrow-left').each(function () {
                    $(this).trigger('click');
                });
            });
            $('.timetable-controls-date-buttons-change-next').off('click').on('click', function () {
                $('div[type="button"].jqx-rc-all').find('.jqx-icon-arrow-right').each(function () {
                    $(this).trigger('click');
                });
            });
        },

        /**
         * This method binds elements.
         * @private
         */
        _initScheduler: function() {
            var self = this,
                scheduler = $(this.options.scheduler);

            scheduler.jqxScheduler({
                date: new $.jqx.date('todayDate'),
                width: 700,
                source: new $.jqx.dataAdapter(this.getSource()),
                view: 'dayView',
                theme: 'energyblue',
                dayNameFormat: "abbr",
                showHeader: false,
                showToolbar: false,
                // showAllDayRow: false,
                ready: function () {
                    scheduler.jqxScheduler('ensureAppointmentVisible', 'id1');
                },
                resources: {
                    colorScheme: "scheme02",
                    dataField: "calendar",
                    source: new $.jqx.dataAdapter(this.getSource())
                },
                appointmentDataFields: {
                    from: "start",
                    to: "end",
                    id: "id",
                    description: "description",
                    subject: "subject",
                    resourceId: "calendar"
                },
                views: [
                    { type: "dayView", showWeekends: false, timeRuler: { scaleStartHour: 9, scaleEndHour: 20 } }
                ]
            });
            scheduler.on('appointmentAdd', function (event) {
                var args = event.args;
                var appointment = args.appointment;
            });
            scheduler.on('appointmentChange', function (event) {
                var args = event.args;
                var appointment = args.appointment;
            });
        },

        getSource: function () {
            var appointments = [
                {
                    id: "id1",
                    description: "George brings projector for presentations.",
                    location: "",
                    subject: "Quarterly Project Review Meeting",
                    calendar: "Room 1",
                    start: new Date(2022, 1, 30, 9, 0, 0),
                    end: new Date(2022, 1, 30, 16, 0, 0)
                },
                {
                    id: "id2",
                    description: "",
                    location: "",
                    subject: "IT Group Mtg.",
                    calendar: "Room 2",
                    start: new Date(2022, 1, 30, 10, 0, 0),
                    end: new Date(2022, 1, 30, 15, 0, 0)
                }
            ];

            return {
                dataType: "json",
                // dataFields: [
                //     { name: 'id', type: 'string' },
                //     { name: 'description', type: 'string' },
                //     { name: 'location', type: 'string' },
                //     { name: 'subject', type: 'string' },
                //     { name: 'calendar', type: 'string' },
                //     { name: 'start', type: 'date' },
                //     { name: 'end', type: 'date' }
                // ],
                dataFields: [
                    { name: 'id', type: 'string' },
                    { name: 'status', type: 'string' },
                    { name: 'about', type: 'string' },
                    { name: 'address', type: 'string' },
                    { name: 'company', type: 'string'},
                    { name: 'name', type: 'string' },
                    { name: 'style', type: 'string' },
                    { name: 'calendar', type: 'string' },
                    { name: 'start', type: 'date', format: "yyyy-MM-dd HH:mm" },
                    { name: 'end', type: 'date', format: "yyyy-MM-dd HH:mm" }
                ],
                id: 'id',
                localData: appointments
            };
        }
    });

    return $.perfect.timetable;
});