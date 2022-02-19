define([
    'jquery',
    'timetableEventService',
    'jquery-ui-modules/autocomplete',
    'domReady!'
], function ($, timetableEvent) {
    'use strict';

    $.widget('perfect.timetable',{
        options: {
            scheduler: null,
            jqxBtn: null,
            log: null,
            events: [],
            clientIdElement: '#client_id',
            clientNameElement: '#client_name',
            clientPhoneElement: '#client_phone',
            clientEmailElement: '#client_email',
            searchConfig: {}
        },
        lastEventId: null,

        /**
         * Initialize widget
         */
        _create: function() {
            this._initScheduler();
            this._initEvents();
        },

        /**
         * This method binds elements.
         * @private
         */
        _initScheduler: function() {
            var self = this,
                scheduler = $(self.options.scheduler),
                source = new $.jqx.dataAdapter(this.getSource());

            return new Promise(function() {
                scheduler.jqxScheduler({
                    date: new $.jqx.date('todayDate'),
                    width: self.getWidth(),
                    height: 800,
                    source: source,
                    view: 'dayView',
                    // showHeader: false,
                    // showToolbar: false,
                    // showAllDayRow: false,
                    localization: self._getLocalization(),
                    ready: function () {
                        scheduler.jqxScheduler('ensureEventVisible', self.lastEventId);
                    },
                    resources: {
                        colorScheme: "scheme04",
                        dataField: "master",
                        source: source
                    },
                    eventDataFields: {
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
                        {
                            type: "dayView",
                            showWeekends: true,
                            timeRuler: {hidden: false,scaleStartHour: 8, scaleEndHour: 20},
                            workTime: {fromDayOfWeek: 1, toDayOfWeek: 5, fromHour: 8, toHour: 20}
                        }
                    ],
                    renderEvent: function (info) {
                        var startedAt = (new Date(Date.parse(info.event.from.toDate()))),
                            finishedAt = (new Date(Date.parse(info.event.to.toDate())));

                        var eventInfo = '<div><span>';
                        eventInfo += startedAt.toLocaleString("en-GB", {hour: "numeric", minute: "2-digit"});
                        eventInfo += ' - ';
                        eventInfo += finishedAt.toLocaleString("en-GB", {hour: "numeric", minute: "2-digit"});
                        eventInfo += '</span></div>';
                        eventInfo += '<div><span>' + info.event.subject + '</span></div>';

                        info.html = eventInfo;

                        return info;
                    },
                    editDialogCreate: function (dialog, fields, editEvent) {
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
                            "<input type='text' id='client_name'" +
                            " class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all'" +
                            " style='width: 100%; height: 25px; box-sizing: border-box;'></div>";
                        clientContactContainer += '<input type=\'hidden\' id=\'client_id\'"/>';

                        clientContactContainer += "<div><div class='jqx-scheduler-edit-dialog-label'>Телефон</div>";
                        clientContactContainer += "<div class='jqx-scheduler-edit-dialog-field'>"
                            + "<input type='text' id='client_phone'" +
                            " class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all'" +
                            " style='width: 100%; height: 25px; box-sizing: border-box;'></div>";

                        clientContactContainer += "<div><div class='jqx-scheduler-edit-dialog-label'>Email</div>";
                        clientContactContainer += "<div class='jqx-scheduler-edit-dialog-field'>" +
                            "<input type='text' id='client_email'" +
                            " class='jqx-widget-content jqx-input-widget jqx-input jqx-widget jqx-rc-all'" +
                            " style='width: 100%; height: 25px; box-sizing: border-box;'></div>";

                        fields.subjectContainer.prepend(clientContactContainer);

                        self._initAutocomplete();
                    },
                    editDialogOpen: function (dialog, fields, editEvent) {
                        console.log('open dialog');
                        $(self.options.clientIdElement).val('');
                        $(self.options.clientNameElement).val('');
                        $(self.options.clientPhoneElement).val('');
                        $(self.options.clientEmailElement).val('');
                    },
                    editDialogClose: function (dialog, fields, editEvent) {
                    }
                });
                scheduler.on('eventAdd', self._saveEvent.bind(this));
                scheduler.on('eventChange', self._saveEvent.bind(this));
                scheduler.on('eventDelete', self._deleteEvent.bind(this));
            });
        },

        getWidth: function () {
            InitResponse();

            var response = new $.jqx.response(),
                scheduler = $(this.options.scheduler),
                width = 750;

            if (response.device.type === "Phone") {
                if (scheduler) {
                    scheduler.css('marginLeft', '5%');
                }
                width = '90%';
            } else if (response.device.type === "Tablet") {
                var windowWidth = document.body.offsetWidth - 50;
                if (windowWidth > 850) {
                    windowWidth = 850;
                }
                if (scheduler) {
                    scheduler.css('marginLeft', 'auto');
                    scheduler.css('marginRight', 'auto');
                }
                width = windowWidth;
            } else {
                scheduler.css('margin-right', '5px');
            }

            return width;
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
                minLength: 2,
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
                messages: {
                    noResults: 'Клиенты не найдены',
                    results: function (amount) {
                        return '';
                    }
                },
                select: function (event, ui) {
                    var item = ui.item;

                    $(self.options.clientNameElement).val(item.firstname);
                    $(self.options.clientPhoneElement).val(item.phone);
                    $(self.options.clientEmailElement).val(item.email);
                }
            });
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
                localData: this._populateEvents()
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
                editRecurringEventDialogTitleString: "Изменить повторающуюся запись",
                editRecurringEventDialogContentString: "Вы ходить изменить только эту запись или серию?",
                editRecurringEventDialogOccurrenceString: "Изменить запись",
                editRecurringEventDialogSeriesString: "Редактировать серию",
                editDialogTitleString: "Изменить запись",
                editDialogCreateTitleString: "Создать новую запись",
                contextMenuEditEventString: "Изменить запись",
                contextMenuCreateEventString: "Создать новую запись",
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

        _populateEvents: function () {
            var events = [];
            for (var entityId in this.options.events) {
                var event = this.options.events[entityId];

                var regex = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/,
                    startedAt, finishedAt;

                startedAt = new Date(event.started_at.replace(regex, '$1-$2-$3'));
                finishedAt = new Date(event.finished_at.replace(regex, '$1-$2-$3'));

                startedAt.setHours(
                    event.started_at.replace(regex, '$4'),
                    event.started_at.replace(regex, '$5')
                );
                finishedAt.setHours(
                    event.finished_at.replace(regex, '$4'),
                    event.finished_at.replace(regex, '$5')
                );

                events.push({
                    id: event.id,
                    subject: event.subject,
                    description: event.description,
                    location: "place",
                    master: event.master,
                    started_at: startedAt.toString(),
                    finished_at: finishedAt.toString()
                });

                this.lastEventId = event.id;
            }

            return events;
        },

        _saveEvent: function (event) {
            timetableEvent.sendEvent(event.args.event);
        },

        _deleteEvent: function (event) {
            timetableEvent.deleteEvent(event.args.event);
        }
    });

    return $.perfect.timetable;
});