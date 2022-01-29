define([
    'Magento_Ui/js/grid/columns/date',
    'moment'
], function (Column, moment) {
    'use strict';

    return Column.extend({
        /**
         * @inheritDoc
         */
        getLabel: function (record) {
            let startTime = moment(record['start_time']),
                endTime = moment(record['end_time']);

            return startTime.isValid() && endTime.isValid()
                ? startTime.format(this.dateFormat) + ' - ' +  endTime.format(this.dateFormat)
                : '';
        }
    });
});
