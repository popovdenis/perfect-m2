define([
    'underscore',
    'Magento_Ui/js/form/element/select'
], function (_, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            listens: {
                '${ $.provider }:form.loaded': 'onFormLoaded'
            }
        },

        /**
         * Trigger update value
         */
        triggerUpdateValue: function () {
            this.value.valueHasMutated();
        },

        /**
         * Filter options
         *
         * @param {string} value
         * @param {string} field
         */
        filterOptions: function (value, field) {
            var source = this.initialOptions,
                valueBefore = this.value(),
                result;

            result = _.filter(source, function (item) {
                return item[field] !== value;
            });

            this.setOptions(result);
            this.value(valueBefore);
        },

        /**
         * On form loaded handler
         */
        onFormLoaded: function () {
            this.switcherConfig.enabled = true;
            this.initSwitcher();
        }
    });
});
