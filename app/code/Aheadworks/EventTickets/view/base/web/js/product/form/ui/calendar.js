define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'moment',
    'uiRegistry',
    'awFullCalendar',
    'awFullCalendarDayGrid',
    'awFullCalendarTimeGrid',
    'awFullCalendarLocales',
    'moment-timezone-with-data',
], function ($, _, Abstract, moment, registry, FullCalendar) {
    'use strict';

    return Abstract.extend({
        defaults: {
            dataKey: 'data.recurring',
            optionsKey: 'awEtViewOptions',
            calendarContainerSelector: '.aw_et-calendar',
            template: 'Aheadworks_EventTickets/product/form/ui/calendar',
            calendarObject: {},
            peopleFilterQty: 1,
            unavailableEvents: {},
            hiddenDays: [],
            timeSlots: [],
            daysToDisplay: '',
            disableColor: '#efefef',
            eventTextColor: '#000000',
            defaultColor: '#f8fbfe',
            selectColor: '#3788d8',
            currentEvent: {},
            currentEventId: '',
            currentTimeSlot: '',
            weeksRepeatCount: '',
            monthDays: '',
            deadlineCorrections: [],
            timeZone: '',
            displayedTime: [],
            dateBrackets: [],
            convertFormat: 'YYYY-MM-DD[T]HH:mm:ss'
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .track({
                    currentEventId: '',
                    currentTimeSlot: ''
                });

            return this;
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            let calendar,
                calendarEl,
                self = this;

            this._super();
            if ($(this.calendarContainerSelector).length) {
                this._setData();

                if (parseInt(this.daysToDisplay) > 0 && this.dateBrackets) {
                    let endDate,
                        xDay = moment().add(parseInt(this.daysToDisplay) - 1,'days').format('YYYY-MM-DD');

                    if (this.dateBrackets) {
                        endDate = this.dateBrackets.endDate;

                        if (endDate !== undefined && moment(endDate) > moment(xDay)) {
                            this.dateBrackets.endDate = moment(xDay).format('YYYY-MM-DD HH:mm:ss')
                        }
                    }
                }

                calendarEl = $(this.calendarContainerSelector)[0];
                calendar = new FullCalendar.Calendar(calendarEl, {
                    displayEventTime: true,
                    locale: this.getData('locale'),
                    timeFormat: 'hh:mm a',
                    now: self._convertToTimezone(null, self.timeZone).format(),
                    eventLimit: true,
                    height: 'auto',
                    defaultView: 'timeGridWeek',
                    firstDay: (parseInt(this.daysToDisplay) > 0 && this.dateBrackets)? moment().day() : '',
                    nowIndicator: true,
                    displayEventEnd: true,
                    titleFormat: {year: 'numeric', month: 'long'},
                    plugins: ['dayGrid', 'timeGrid'],
                    eventTextColor: self.eventTextColor,
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short',
                        hour12: true
                    },
                    header: {
                        left: '',
                        center: 'title',
                        right: 'prev,next dayGridMonth,timeGridWeek'
                    },
                    views: {
                        timeGridWeek: {
                            columnHeaderFormat: {
                                weekday: 'long',
                                day: 'numeric'
                            },
                            slotLabelFormat: {
                                hour: 'numeric',
                                minute: '2-digit',
                                meridiem: 'short',
                                hour12: true
                            }
                        },
                        dayGridMonth: {
                            columnHeaderFormat: {
                                weekday: 'long'
                            }
                        }
                    },
                    minTime: self.displayedTime.minTime,
                    maxTime: self.displayedTime.maxTime,
                    editable: false,
                    allDaySlot: false,
                    weekNumbers: false,
                    eventClick: function (info) {
                        self.calendarEventClickHandler(info, this);
                    },
                    dayRender: function (info) {
                        self.calendarDayRender(info, this);
                    },
                    eventRender: function(info) {
                        $(info.el).append('<button type="button" class="aw-et-remove-event"></button>');
                        $(info.el).on('click', '.aw-et-remove-event', function(e){
                            self.calendarEventDoubleClickHandler(info.event.id);
                            e.stopImmediatePropagation();
                        });
                        $(info.el).on('dblclick', function() {
                            self.calendarEventDoubleClickHandler(info.event.id);
                        });
                    },
                    columnHeaderHtml: function (date) {
                        return self.columnHeaderHtml(date);
                    }
                });

                this.hideSectors();
                calendar.render();
                this.calendarObject = calendar;
            }
        },

        /**
         * Calendar day render
         *
         * @param info
         * @param calendar
         */
        calendarDayRender: function (info, calendar) {
            let self = this,
                eventColor,
                textColor,
                startDateTime,
                endDateTime,
                id;

            if (self.hiddenDays.indexOf(moment(info.date).day()) !== -1) {
                if (moment().format('YYYY-MM-DD') !== moment(info.date).format('YYYY-MM-DD')) {
                    $(info.el).css('backgroundColor', self.disableColor); //Disabled days
                }
                return;
            }

            if (!this.isEventCanBeShown(info.date)) {
                if (parseInt(this.daysToDisplay) > 0) {
                    $(info.el).css('backgroundColor', self.disableColor);
                }
                
                return;
            }

            if (moment(info.date).format('YYYY-MM-DD') >= moment().format('YYYY-MM-DD')) {
                _.each(this.timeSlots, function (slot, timeSlotId) {
                    eventColor = self.defaultColor;
                    textColor = self.textColor;
                    startDateTime = moment(info.date).format('YYYY-MM-DD') + 'T' + slot.startTime;
                    endDateTime = moment(info.date).format('YYYY-MM-DD') + 'T' + slot.endTime;
                    id = moment(info.date).format('YYYY-MM-DD') + ' ' + slot.startTime;

                    if (!calendar.getEventById(id)
                        && !self._isEventUnavailable(id)
                        && !self._isTicketSellingDeadline(id)
                    ) {
                        calendar.addEvent({
                            'start': startDateTime,
                            'end': endDateTime,
                            'id': id,
                            'timeSlotId': timeSlotId,
                            'timeSlotRange': moment(startDateTime).format('hh:mm A') + ' - ' + moment(endDateTime).format('hh:mm A'),
                            'timeSlotDate': moment(info.date).format('MMM YYYY, D'),
                            'backgroundColor': eventColor,
                            'textColor': textColor
                        });
                    }

                    var event = calendar.getEventById(id);

                    if (event && self.peopleFilterQty > 1) {
                        let sectorsQty = self.getData('sectorQty.' + id),
                            defaultSectorsQty = self.getData('sectorDefaultQty'),
                            currentQty = Object.assign(
                                _.clone(defaultSectorsQty),
                                sectorsQty
                            ),
                            eventQty = 0;

                        _.each(currentQty, function (qty, sectorId) {
                            eventQty+= qty;
                        });

                        if (self.peopleFilterQty > eventQty) {
                            event.setProp('backgroundColor', '#f7f7f7');
                            event.setProp('textColor', self.eventTextColor);
                            event.setProp('borderColor', '#f7f7f7');
                            event.setProp('classNames', ['_disabled']);
                            let config = {
                                'eventId': id,
                            };
                            registry.get(self.optionsKey, function (optionsElem) {
                                optionsElem.removeTimeSlotSectors(config);
                            });
                        } else {
                            if (typeof event.classNames !== 'undefined' && event.classNames.length > 0){
                                event.setProp('backgroundColor', self.defaultColor);
                                event.setProp('textColor', self.eventTextColor);
                                event.setProp('borderColor', '');
                                event.setProp('classNames', []);
                            }
                        }
                    }
                });
            }
        },

        /**
         * Calendar event click handler
         *
         * @param info
         * @param calendar
         */
        calendarEventClickHandler: function (info, calendar) {
            let config;

            if (!this.getData('isTimeSlotMultiSelectionAllowed') && _.size(this.currentEvent)) {
                this.currentEvent.setProp('backgroundColor', this.defaultColor);
                this.currentEvent.setProp('textColor', this.eventTextColor);
                config = {
                    'eventId': this.currentEventId,
                };
                registry.get(this.optionsKey, function (optionsElem) {
                    optionsElem.removeTimeSlotSectors(config);
                });
            }

            this.currentEvent = info.event;
            this.currentEventId = info.event.id;
            this.currentTimeSlot = info.event.extendedProps.timeSlotId;
            this.currentEvent.setProp('backgroundColor', this.selectColor);
            this.currentEvent.setProp('textColor', this.defaultColor);
            this.currentEvent.setProp('classNames', ['active-slot']);
            config = {
                'eventId': info.event.id,
                'timeSlotId': info.event.extendedProps.timeSlotId,
                'timeSlotRange': info.event.extendedProps.timeSlotRange,
                'timeSlotDate': info.event.extendedProps.timeSlotDate,
                'sectorQty': this._getSectorQty(info.event.id)
            };
            registry.get(this.optionsKey, function (optionsElem) {
                optionsElem.addTimeSlotSectors(config);
            });
        },

        /**
         * Calendar event double click handler
         *
         * @param eventId
         */
        calendarEventDoubleClickHandler: function (eventId) {
            this.currentEvent = this.calendarObject.getEventById(eventId);
            if (_.size(this.currentEvent)) {
                this.currentEvent.setProp('backgroundColor', this.defaultColor);
                this.currentEvent.setProp('textColor', this.eventTextColor);
                this.currentEvent.setProp('classNames', []);
            }

            let config = {
                'eventId': eventId,
            };
            registry.get(this.optionsKey, function (optionsElem) {
                optionsElem.removeTimeSlotSectors(config);
            });
        },

        /**
         * Check if event can be shown on calendar
         *
         * @param date
         * @returns {boolean}
         */
        isEventCanBeShown: function(date) {
            let weekDiff,
                startDate,
                endDate;

            if (this.weeksRepeatCount) {
                startDate = this.dateBrackets.startDate;
                weekDiff = moment(date).diff(moment(startDate), 'week');

                if (weekDiff < 0 || (weekDiff) % this.weeksRepeatCount !== 0) {
                    return false;
                }
            }

            if (this.monthDays) {
                if (this.monthDays.indexOf(moment(date).format('D')) === -1) {
                    return false;
                }
            }

            if (this.dateBrackets) {
                startDate = this.dateBrackets.startDate;
                endDate = this.dateBrackets.endDate;

                if (startDate !== undefined && moment(startDate) > moment(date)) {
                    return false;
                }

                if (endDate !== undefined && moment(endDate) < moment(date)) {
                    return false;
                }
            }

            return true;
        },

        /**
         * Column header render
         *
         * @param {Date} date
         * @return {string}
         */
        columnHeaderHtml: function (date) {
            let dayName = moment(date.toISOString()).format('dddd'),
                dayNumber = moment(date.toISOString()).format('D'),
                currentDay = this.isToday(date) ? 'current-day' : '';

            return '<span class="aw-et__header-day-name">'+ dayName + '</span>&nbsp;'
                + '<span class="aw-et__header-day-number '+ currentDay + '">' + dayNumber + '</span>';
        },

        /**
         * Check if date is current date
         *
         * @param {Date} date
         * @returns {boolean}
         */
        isToday: function (date) {
            return this._convertToTimezone(null, this.timeZone).isSame(moment(date), 'd');
        },

        /**
         * Check if date is current date
         *
         * @param peopleQty
         * @returns void
         */
        filterTimeSlots: function (peopleQty) {
            this.peopleFilterQty = peopleQty;
            this.calendarObject.destroy();
            this.calendarObject.render();
            let typeView = this.calendarObject.view.type;
            this.calendarObject.changeView(typeView);
        },

        /**
         * Check if ticket selling deadline reached
         *
         * @param date
         * @returns {boolean}
         * @private
         */
        _isTicketSellingDeadline: function (date) {
            if (this.deadlineCorrections) {
                let days = this.deadlineCorrections.days,
                    hours = this.deadlineCorrections.hours,
                    minutes = this.deadlineCorrections.minutes,
                    eventDate = moment(date).subtract(days, 'days').subtract(hours, 'hours').subtract(minutes, 'minutes');

                if (this._convertToTimezone(null, this.timeZone) >= eventDate) {
                    return true;
                }
            } else {
                if (this._convertToTimezone(null, this.timeZone) >= moment(date)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Hide all sectors if no event to choose
         */
        hideSectors: function () {
            let self = this,
                sector,
                defaultSectorsQty = this.getData('sectorDefaultQty');

            _.each(defaultSectorsQty, function (value, sectorId) {
                sector = registry.get(self.optionsKey + '.sector_' + sectorId, function (sector) {
                    sector.visible = false;
                });
            });
        },

        /**
         * Get data
         *
         * @param {String} key
         * @param {Array|Object|String|null} defaultValue
         * @return {Array|Object|String|null}
         */
        getData: function (key, defaultValue = null) {
            let value = this.source.get(this.dataKey + '.' + key);

            return value ? value : defaultValue;
        },

        /**
         * Set calendar data
         * @private
         */
        _setData: function () {
            this.unavailableEvents = JSON.parse(this.getData('unavailableEvents'));
            this.hiddenDays = this.getData('hiddenDays', []);
            this.timeSlots = this.getData('timeSlots');
            this.weeksRepeatCount = this.getData('weeksRepeatCount');
            this.monthDays = this.getData('monthDays');
            this.deadlineCorrections = this.getData('deadlineCorrections');
            this.timeZone = this.getData('timezone');
            this.displayedTime = this.getData('displayedTime', []);
            this.dateBrackets = this.getData('dateBrackets');
            this.daysToDisplay = this.getData('daysToDisplay');
        },

        /**
         * Check if events already in pool
         *
         * @param eventId
         * @private
         */
        _isEventUnavailable: function (eventId) {
            return this.unavailableEvents.indexOf(eventId) !== -1;
        },

        /**
         * Get sector tickets qty for event date
         *
         * @param eventId
         * @private
         */
        _getSectorQty: function (eventId) {
            let sectorsQty = this.getData('sectorQty.' + eventId),
                defaultSectorsQty = this.getData('sectorDefaultQty');

            return Object.assign(_.clone(defaultSectorsQty), sectorsQty);
        },

        /**
         * Prepare datetime according to needed timezone.
         * Workaround for moment js which convert datetime from any timezone to UTC when compare
         *
         * @param time
         * @param timezone
         * @returns {*}
         * @private
         */
        _convertToTimezone: function (time, timezone) {
            return moment(moment(time ? time : []).tz(timezone ? timezone : 'UTC').format(this.convertFormat));
        }
    });
});
