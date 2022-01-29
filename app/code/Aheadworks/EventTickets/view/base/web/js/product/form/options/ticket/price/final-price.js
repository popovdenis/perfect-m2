define([
    'Magento_Catalog/js/product/list/columns/final-price',
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * Initializes observable properties of instance
         *
         * @returns {Component} Chainable
         */
        initObservable: function () {
            this._super()
                .track('priceWrapperCssClasses');

            return this;
        },
    });
});
