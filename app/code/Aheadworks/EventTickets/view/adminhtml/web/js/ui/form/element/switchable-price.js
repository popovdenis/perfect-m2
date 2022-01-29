define([
    'Magento_Ui/js/form/element/abstract',
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                '${ $.provider }:hideFields': 'hide',
                '${ $.provider }:showFields': 'show',
            },
        },

        /**
         * Hide field
         */
        hide: function() {
            this.visible(false);
        },

        /**
         * Show field
         */
        show: function() {
            this.visible(true);
        },
    });
});
