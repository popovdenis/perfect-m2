define([
    'Aheadworks_EventTickets/js/product/form/options/sector/product/options-renderer/renderer-abstract'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            listens: {
                '${ $.provider }:${ $.customScope ? $.customScope + "." : ""}data.validate': 'validate',
            },
            modules: {
                configurable: '${ $.parent }'
            },
        },
        _optionDeps: [],

        /**
         * Resolve loader status data
         *
         * @param {Array} optionDeps
         * @private
         */
        _resolveLoaderStatusData: function (optionDeps) {
            this.configurable().set('loaderStatusData', { 'optionDeps': optionDeps });
        }
    });
});
