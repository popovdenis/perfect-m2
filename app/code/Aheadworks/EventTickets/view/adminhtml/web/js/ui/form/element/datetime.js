define([
    'mageUtils',
    'Magento_Ui/js/form/element/date'
], function (utils, Date) {
    'use strict';

    return Date.extend({
        /**
         * @inheritdoc
         */
        prepareDateTimeFormats: function () {
            this._super();
            if (this.options.showsTime && this.timezoneFormat) {
                this.validationParams.dateFormat = this.timezoneFormat;
            }
        }
    });
});