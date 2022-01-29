define([
    'moment',
    'mageUtils',
    'Magento_Ui/js/form/element/date'
], function (moment, utils, Date) {
    'use strict';

    return Date.extend({
        /**
         * @inheritdoc
         */
        prepareDateTimeFormats: function () {
            if (this.options.dateFormat) {
                this.outputDateFormat = this.options.dateFormat;
            }

            this.inputDateFormat = utils.convertToMomentFormat(this.inputDateFormat);
            this.outputDateFormat = utils.convertToMomentFormat(this.outputDateFormat);
            this.pickerDateTimeFormat = utils.convertToMomentFormat(this.options.timeFormat);
            this.options.dateFormat = this.options.timeFormat;
            this.validationParams.dateFormat = this.options.timeFormat;
        },

        /**
         * @inheritDoc
         */
        onUpdate: function () {
            this.validate();
            this.bubble('update', this.hasChanged());
        },

        /**
         * @inheritdoc
         */
        onShiftedValueChange: function (shiftedValue) {
            var value,
                formattedValue,
                momentValue;

            if (shiftedValue) {
                momentValue = moment(shiftedValue, this.pickerDateTimeFormat);

                if (this.options.showsTime) {
                    formattedValue = moment(momentValue).format(this.timezoneFormat);
                    value = moment.tz(formattedValue, this.storeTimeZone).tz('UTC').toISOString();
                } else {
                    value = momentValue.format(this.outputDateFormat);
                }
            } else {
                value = '';
            }

            if (value !== this.value()) {
                this.value(value);
            }
        },

        /**
         * @inheritdoc
         */
        onValueChange: function (value) {
            var shiftedValue;

            if (value) {
                if (this.options.showsTime) {
                    shiftedValue = moment.tz(value, 'UTC').tz(this.storeTimeZone);
                } else {
                    shiftedValue = moment(value, this.outputDateFormat);
                }

                if (!shiftedValue.isValid()) {
                    shiftedValue = moment(value, this.inputDateFormat);
                }
                shiftedValue = shiftedValue.format(this.pickerDateTimeFormat);
            } else {
                shiftedValue = '';
            }

            if (shiftedValue !== this.shiftedValue()) {
                this.shiftedValue(shiftedValue.toUpperCase());
            }
        },
    });
});