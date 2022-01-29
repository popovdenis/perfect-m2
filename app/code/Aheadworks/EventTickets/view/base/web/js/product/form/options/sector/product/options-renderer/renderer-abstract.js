define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            options: {}
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();

            return this;
        }
    });
});
