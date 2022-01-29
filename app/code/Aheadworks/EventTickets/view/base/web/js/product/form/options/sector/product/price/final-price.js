define([
    'Aheadworks_EventTickets/js/product/form/options/ticket/price/final-price',
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * Get product regular price.
         *
         * @param {Object} row
         * @return {HTMLElement} regular price html
         */
        getRegularPrice: function (row) {
            this.source.set(this.dataScope + '.exclTaxPrice', this.getExclTaxPrice(row));
            this.source.set(this.dataScope + '.price', this.preparePrice(row));
            return this._super(row);
        },

        /**
         * Prepare product price.
         *
         * @param {Object} row
         * @return {Number} regular price html
         */
        preparePrice: function (row) {
            if (this.hasSpecialPrice(row)) {
                return row['price_info']['final_price'];
            } else {
                return row['price_info']['regular_price'];
            }
        },

        /**
         * Get excluded tax price depending on price type. It is used for calculating tax.
         *
         * @param {Object} row
         * @returns {Number}
         */
        getExclTaxPrice: function (row) {
            if (this.hasSpecialPrice(row)) {
                return row['price_info']['extension_attributes']['tax_adjustments']['final_price'];
            } else {
                return row['price_info']['extension_attributes']['tax_adjustments']['regular_price'];
            }
        },
    });
});
