define([
    'jquery',
    'priceBox'
], function ($) {
    'use strict';

    $.widget('mage.priceBox', $.mage.priceBox, {

        /**
         * {@inheritdoc}
         */
        reloadPrice: function () {
            this._super();
            this.updatePriceDataAttribute();
        },

        /**
         * Update price data attribute with final price
         */
        updatePriceDataAttribute() {
            _.each(this.cache.displayPrices, function (price, priceCode) {
                price.final = _.reduce(price.adjustments, function (memo, amount) {
                    return memo + amount;
                }, price.amount);

                $('[data-price-type="' + priceCode + '"]', this.element).attr('data-price-amount', price.final);
            }, this);

            $(document).trigger('awEtPriceUpdated_' + this.options.uniqueId);

        }
    });

    return $.mage.priceBox;
});
