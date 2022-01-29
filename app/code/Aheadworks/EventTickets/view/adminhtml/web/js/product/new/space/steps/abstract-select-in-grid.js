define([
    'uiComponent',
    'underscore'
], function (Component, _) {
    'use strict';

    return Component.extend({
        defaults: {
            notificationMessage: {
                text: null,
                error: null
            },
            modules: {
                multiSelect: '${ $.multiSelectName }'
            }
        },

        /**
         * Render step
         *
         * @param {Object} wizard
         */
        render: function (wizard) {
            this.wizard = wizard;
        },

        /**
         * Back action
         */
        back: function () {

        },

        /**
         * Retrieve selected venue row data
         *
         * @param {String} selectedValue
         * @return {Object}
         */
        getSelectedRowData: function (selectedValue) {
            var rowData;

            rowData = _.findWhere(this.multiSelect().rows(), {
                'id': selectedValue
            });

            return rowData;
        },

        /**
         * Retrieve selected value
         *
         * @return {Number}
         */
        getSelectedValue: function () {
            return this.multiSelect().selected();
        }
    });
});
