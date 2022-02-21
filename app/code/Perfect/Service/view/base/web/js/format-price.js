define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Catalog/js/price-utils'
], function (Element, priceUtils) {
    'use strict';

    return Element.extend({
        defaults: {
            'defaultText': '',
        },
        setInitialValue: function() {
            this._super();
            var self = this;

            self.setFormatPrice(self.initialValue);
            return this;
        },

        setFormatPrice: function(value) {
            var priceFormat = {
                decimalSymbol: ",",
                groupLength: ",",
                groupSymbol: ",",
                integerRequired: 1,
                requiredPrecision: 0
            };
            var price = (value) ? priceUtils.formatPrice(value, priceFormat) : this.defaultText;
            this.initialValue = price;
            this.value._latestValue = price;

            return this;
        }
    });
});