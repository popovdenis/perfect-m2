define([
    'Magento_Tax/js/price/adjustment',
    'uiRegistry'
], function (Component, registry) {
    'use strict';

    return Component.extend({

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            this.initAsyncListens();

            return this;
        },

        /**
         * Init async listens and trigger init of price attributes
         */
        initAsyncListens: function () {
            registry.async(this.parentName)(
                function () {
                    this.initializePriceAttributes();
                }.bind(this)
            );
        },
    });
});
