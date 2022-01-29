define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'moment',
    'mage/translate'
], function (_, DynamicRows, moment, $t) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            errorMessage: $t('You have invalid or intersect time value.'),
            listens: {
                '${ $.provider }:data.validate': 'validateRows'
            }
        },

        /**
         * @inheritdoc
         */
        onChildrenUpdate: function () {
            this._super();
            this.validateRows();
        },

        /**
         * Rows data validation
         */
        validateRows: function () {
            var rowsIndexes = this.getInvalidRowIndexes(),
                isValid = _.isEmpty(rowsIndexes);

            this.clearErrors();
            if (!isValid) {
                this.source.set('params.invalid', true);
                this.displayErrorsForRows(rowsIndexes);
            }
        },

        /**
         * Retrieve invalid rows data indexes
         *
         * @returns {Array}
         */
        getInvalidRowIndexes: function () {
            var rowIndexes = [],
                rowsToProcess = this.getChildItems(),
                processedIndex,
                processedItem,
                me = this;

            if (rowsToProcess.length == 1 && !this.isRowValid(_.first(rowsToProcess))) {
                rowIndexes.push(0);
            } else {
                _.each(rowsToProcess, function (item, index) {
                    processedIndex = index;
                    processedItem = item;

                    _.each(rowsToProcess, function (item, index) {
                        if (index > processedIndex
                            && me.hasIntersectValues(processedItem, item)
                        ) {
                            rowIndexes.push(processedIndex, index);
                        }
                    });
                });
            }

            return _.uniq(rowIndexes);
        },

        /**
         * Check is object have intersect values
         *
         * @param {Object} obj1
         * @param {Object} obj2
         * @returns {boolean}
         */
        hasIntersectValues: function (obj1, obj2) {
            var result = false;

            if (obj1.start_time && obj1.end_time && obj2.start_time && obj2.end_time) {
                if (!this.isRowValid(obj1) || !this.isRowValid(obj2)) {
                    result = true;
                }

                if (moment(obj1.start_time).isBetween(obj2.start_time, obj2.end_time, 'second')
                    || moment(obj1.end_time).isBetween(obj2.start_time, obj2.end_time, 'second')
                    || moment(obj2.start_time).isBetween(obj1.start_time, obj1.end_time, 'second')
                    || moment(obj2.end_time).isBetween(obj1.start_time, obj1.end_time, 'second')
                    || (moment(obj1.start_time).isSame(obj2.start_time) && moment(obj1.end_time).isSame(obj2.end_time))
                ) {
                    result = true;
                }
            }

            return result;
        },

        /**
         * Is row valid
         *
         * @param {Object} row
         * @return {boolean|*}
         */
        isRowValid: function (row) {
            return !!row.start_time && row.end_time && moment(row.start_time).isBefore(row.end_time);
        },

        /**
         * Display errors
         *
         * @param {Array} rowIndexes
         */
        displayErrorsForRows: function (rowIndexes) {
            var me = this;

            _.each(this.elems(), function (elem, index) {
                if (_.contains(rowIndexes, index)) {
                    elem.elems.map('error', me.errorMessage);
                }
            });
        },

        /**
         * Clear errors
         */
        clearErrors: function () {
            _.each(this.elems(), function (elem) {
                elem.elems.map('error', null);
            });
        }
    });
});
