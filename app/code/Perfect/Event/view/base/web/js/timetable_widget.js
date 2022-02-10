/**
 * Widget manages tabs content loading by ajax
 */
define([
    'jquery',
    'timetableAppointmentService',
    'jquery-ui-modules/autocomplete',
    'domReady!'
], function ($, timetableAppointment) {
    'use strict';

    $.widget('perfect.timetable',{
        options: {
            scheduler: null,
            appointments: [],
            clientNameElement: '#client_name',
            clientPhoneElement: '#client_phone',
            clientEmailElement: '#client_email',
            searchConfig: {}
        },
        lastAppointmentId: null,

        /**
         * Initialize widget
         */
        _create: function() {
            this._initScheduler();
            this._initEvents();
        },

        _initEvents: function () {
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

        _initAutocomplete: function () {
            var self = this;
            $(self.options.clientNameElement).autocomplete({
                minLength: 3,
                source: function(request, response) {
                    $.ajax( {
                        url: self.options.searchConfig.url,
                        dataType: 'json',
                        data: {search: request.term},
                        success: function(results) {
                            if (!results.length) {
                                $("#no-results").text("Клиенты не найдены");
                            } else {
                                $("#no-results").empty();
                            }

                            response(results);
                        }
                    });
                },
                // source: self.options.searchConfig.url,
                // select: function (event, ui) {
                //     $('#customer_id').val(ui.item.customerId);
                //     $('#customer_change').show();
                //     $('#customer_comments').hide();
                //     $('#customer').val(ui.item.label);
                //     $('#customer').prop('disabled', true);
                // },
                messages: {
                    noResults: 'Клиенты не найдены',
                    results: function (amount) {
                        var n = 1;
                    }
                }
                // appendTo: "#city_results"
            });
        },

        /**
         * This method binds elements.
         * @private
         */
        _initScheduler: function() {
            var self = this,
                scheduler = $(this.options.scheduler),
                source = new $.jqx.dataAdapter(this.getSource());

            scheduler.jqxScheduler({
                date: new $.jqx.date('todayDate'),
                width: 600,
                source: source,
                view: 'dayView',
                // showHeader: false,
                // showToolbar: false,
                // showAllDayRow: false,
                localization: self._getLocalization(),
                ready: function () {
                    scheduler.jqxScheduler('ensureAppointmentVisible', this.lastAppointmentId);
                },
                resources: {
                    colorScheme: "scheme04",
                    dataField: "master",
                    source: source
                },
                appointmentDataFields: {
                    from: "started_at",
                    to: "finished_at",
                    id: "id",
                    description: "description",
                    subject: "subject",
                    resourceId: "master",
                    recurrencePattern: "recurrenceRule",
                    recurrenceException: "recurrenceException"
                },
                views: [
                    {type: "dayView", showWeekends: true, timeRuler: {scaleStartHour: 9, scaleEndHour: 20}}
                ],
                editDialogCreate: function (dialog, fields, editAppointment) {
                    fields.locationContainer.hide();
                    fields.timeZoneContainer.hide();
                    fields.subjectLabel.html("Название");
                    fields.descriptionLabel.html("Заметки");
                    fields.fromLabel.html("Начало");
                    fields.toLabel.html("Конец");
                    fields.allDayLabel.html("Весь день");
                    fields.colorLabel.html("Цвет статуса");
                    fields.repeatLabel.html("Повтор");
                    fields.statusLabel.html("Статус");
                    fields.resourceLabel.html("Сотрудник");

                    var clientContactContainer = "<div><div class='jqx-scheduler-edit-dialog-label'>Имя клиента</div>";
                    clientContactContainer += "<div class='jqx-scheduler-edit-dialog-field'>" +
                        "<input type='text' id='client_name' class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all' style='width: 100%; height: 25px; box-sizing: border-box;'></div>";

                    clientContactContainer += "<div><div class='jqx-scheduler-edit-dialog-label'>Телефон</div>";
                    clientContactContainer += "<div class='jqx-scheduler-edit-dialog-field'>"
                        + "<input type='text' id='client_phone' class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all' style='width: 100%; height: 25px; box-sizing: border-box;'></div>";

                    clientContactContainer += "<div><div class='jqx-scheduler-edit-dialog-label'>Email</div>";
                    clientContactContainer += "<div class='jqx-scheduler-edit-dialog-field'>" +
                        "<input type='text' id='client_email' class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all' style='width: 100%; height: 25px; box-sizing: border-box;'></div>";

                    fields.subjectContainer.prepend(clientContactContainer);

                    self._initAutocomplete();
                }
            });
            scheduler.on('appointmentAdd', this._saveAppointment.bind(this));
            scheduler.on('appointmentChange', this._saveAppointment.bind(this));
            scheduler.on('appointmentDelete', this._deleteAppointment.bind(this));
        },

        getSource: function () {
            return {
                dataType: "json",
                dataFields: [
                    { name: 'id', type: 'string' },
                    { name: 'subject', type: 'string' },
                    { name: 'status', type: 'string' },
                    { name: 'about', type: 'string' },
                    { name: 'address', type: 'string' },
                    { name: 'company', type: 'string'},
                    { name: 'name', type: 'string' },
                    { name: 'recurrenceRule', type: 'string' },
                    { name: 'recurrenceException', type: 'string' },
                    { name: 'master', type: 'string' },
                    { name: 'started_at', type: 'date', format: "yyyy-MM-dd HH:mm" },
                    { name: 'finished_at', type: 'date', format: "yyyy-MM-dd HH:mm" }
                ],
                id: 'id',
                localData: this._populateAppointments()
            };
        },

        _getLocalization: function () {
            return {
                "/": ".",
                firstDay: 1,
                days: {
                    names: ["воскресенье","понедельник","вторник","среда","четверг","пятница","суббота"],
                    namesAbbr: ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
                    namesShort: ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"]
                },
                months: {
                    names: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь",""],
                    namesAbbr: ["янв","фев","мар","апр","май","июн","июл","авг","сен","окт","ноя","дек",""]
                },
                monthsGenitive: {
                    names: ["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря",""],
                    namesAbbr: ["янв","фев","мар","апр","май","июн","июл","авг","сен","окт","ноя","дек",""]
                },
                AM: ["AM", "am", "AM"],
                PM: ["PM", "pm", "PM"],
                eras: [
                    // eras in reverse chronological order.
                    // name: the name of the era in this culture (e.g. A.D., C.E.)
                    // start: when the era starts in ticks (gregorian, gmt), null if it is the earliest supported era.
                    { "name": "A.D.", "start": null, "offset": 0 }
                ],
                twoDigitYearMax: 2029,
                patterns: {
                    // short date pattern
                    d: "M/d/yyyy",
                    // long date pattern
                    D: "dddd, MMMM dd, yyyy",
                    // short time pattern
                    t: "h:mm tt",
                    // long time pattern
                    T: "h:mm:ss tt",
                    // long date, short time pattern
                    f: "dddd, MMMM dd, yyyy h:mm tt",
                    // long date, long time pattern
                    F: "dddd, MMMM dd, yyyy h:mm:ss tt",
                    // month/day pattern
                    M: "MMMM dd",
                    // month/year pattern
                    Y: "yyyy MMMM",
                    // S is a sortable format that does not vary by culture
                    S: "yyyy\u0027-\u0027MM\u0027-\u0027dd\u0027T\u0027HH\u0027:\u0027mm\u0027:\u0027ss",
                    // formatting of dates in MySQL DataBases
                    ISO: "yyyy-MM-dd hh:mm:ss",
                    ISO2: "yyyy-MM-dd HH:mm:ss",
                    d1: "dd.MM.yyyy",
                    d2: "dd-MM-yyyy",
                    d3: "dd-MMMM-yyyy",
                    d4: "dd-MM-yy",
                    d5: "H:mm",
                    d6: "HH:mm",
                    d7: "HH:mm tt",
                    d8: "dd/MMMM/yyyy",
                    d9: "MMMM-dd",
                    d10: "MM-dd",
                    d11: "MM-dd-yyyy"
                },
                backString: "Предыдущий",
                forwardString: "Следующий",
                toolBarPreviousButtonString: "Предыдущий",
                toolBarNextButtonString: "Следующий",
                emptyDataString: "Нет данных",
                loadString: "Loading...",
                clearString: "Очистить",
                todayString: "Сегодня",
                dayViewString: "День",
                weekViewString: "Неделя",
                monthViewString: "Месяц",
                timelineDayViewString: "День",
                timelineWeekViewString: "Неделя",
                timelineMonthViewString: "Месяц",
                loadingErrorMessage: "Данные все еще загружаются",
                editRecurringAppointmentDialogTitleString: "Изменить повторающуюся запись",
                editRecurringAppointmentDialogContentString: "Вы ходить изменить только эту запись или серию?",
                editRecurringAppointmentDialogOccurrenceString: "Изменить запись",
                editRecurringAppointmentDialogSeriesString: "Редактировать серию",
                editDialogTitleString: "Изменить запись",
                editDialogCreateTitleString: "Создать новую запись",
                contextMenuEditAppointmentString: "Изменить запись",
                contextMenuCreateAppointmentString: "Создать новую запись",
                editDialogSubjectString: "Название",
                editDialogLocationString: "Место расположения",
                editDialogFromString: "От",
                editDialogToString: "До",
                editDialogAllDayString: "Весь день",
                editDialogExceptionsString: "Исключение",
                editDialogResetExceptionsString: "Сбросить",
                editDialogDescriptionString: "Описание",
                editDialogResourceIdString: "Календарь",
                editDialogStatusString: "Статус",
                editDialogColorString: "Цвет",
                editDialogColorPlaceHolderString: "Выбрать цвет",
                editDialogTimeZoneString: "Часовой пояс",
                editDialogSelectTimeZoneString: "Выберите часовой пояс",
                editDialogSaveString: "Сохранить",
                editDialogDeleteString: "Удалить",
                editDialogCancelString: "Отменить",
                editDialogRepeatString: "Повторять",
                editDialogRepeatEveryString: "Повторять каждый день",
                editDialogRepeatEveryWeekString: "неделю",
                editDialogRepeatEveryYearString: "год",
                editDialogRepeatEveryDayString: "день",
                editDialogRepeatNeverString: "Никогда",
                editDialogRepeatDailyString: "Ежедневно",
                editDialogRepeatWeeklyString: "Каждую неделю",
                editDialogRepeatMonthlyString: "Помесячно",
                editDialogRepeatYearlyString: "Ежегодно",
                editDialogRepeatEveryMonthString: "Каждый месяц",
                editDialogRepeatEveryMonthDayString: "День",
                editDialogRepeatFirstString: "первый",
                editDialogRepeatSecondString: "второй",
                editDialogRepeatThirdString: "третий",
                editDialogRepeatFourthString: "четвертый",
                editDialogRepeatLastString: "пятый",
                editDialogRepeatEndString: "Окончание",
                editDialogRepeatAfterString: "Каждые",
                editDialogRepeatOnString: "Завершить",
                editDialogRepeatOfString: "Выкл",
                editDialogRepeatOccurrencesString: "Напоминание",
                editDialogRepeatSaveString: "Сохранить запись",
                editDialogRepeatSaveSeriesString: "Сохранить серию",
                editDialogRepeatDeleteString: "Удалить запись",
                editDialogRepeatDeleteSeriesString: "Удалить серию",
                editDialogStatuses:
                    {
                        free: "Бесплатно",
                        tentative: "Пробный",
                        busy: "Занят",
                        outOfOffice: "Вне дома"
                    }
            };
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
                    subject: appointment.subject,
                    description: appointment.description,
                    location: "place",
                    master: appointment.master,
                    started_at: startedAt.toString(),
                    finished_at: finishedAt.toString()
                });

                this.lastAppointmentId = appointment.id;
            }

            return appointments;
        },

        _saveAppointment: function (event) {
            timetableAppointment.sendAppointment(event.args.appointment);
        },

        _deleteAppointment: function (event) {
            timetableAppointment.deleteAppointment(event.args.appointment);
        }
    });

    return $.perfect.timetable;
});