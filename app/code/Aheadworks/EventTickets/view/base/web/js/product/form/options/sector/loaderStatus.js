define([
    'underscore',
    'uiComponent'
], function (_, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            exports: {
                isLoading: '${ $.parentName }:isLoading'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .loadingComplete();

            return this;
        },

        /**
         * Loading complete
         */
        loadingComplete: function() {
            this.set('isLoading', false);
        }
    });
});
