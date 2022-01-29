define([
    'uiCollection',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function (uiCollection, _, priceUtils, $t) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/summary',
            imports: {
                selectedOptions: '${ $.provider }:params.selectedOptions',
                selectedProducts: '${ $.provider }:params.selectedProducts',
                priceFormat: '${ $.provider }:data.priceFormat'
            },
            displayBothPrices: '3'
        },

        /**
         * Initializes observable properties
         *
         * @returns {Summary} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    selectedOptions: {},
                    selectedProducts: {}
                });

            return this;
        },

        /**
         * Retrieve selected ticket qty label
         *
         * @returns {String}
         */
        getSelectedTicketQtyLabel: function () {
            var label = $t('{number} ticket(s)');

            return label.replace('{number}', this.getSelectedTicketQty());
        },

        /**
         * Retrieve selected product qty label
         *
         * @returns {String}
         */
        getSelectedProductQtyLabel: function () {
            var label = $t('{number} product(s)');

            return label.replace('{number}', this.getSelectedProductQty());
        },

        /**
         * Check if need to show default summary label
         *
         * @returns {Boolean}
         */
        isNeedToShowDefaultLabel: function() {
            return _.size(this.getSelectedTicketsByTimeSlot()) === 0;
        },

        /**
         * Retrieve selected tickets by time slots
         *
         * @return {Object}
         */
        getSelectedTicketsByTimeSlot: function ()
        {
            var sectors = [], sectorId, ticket, timeSlots = {}, slotId;

            if (_.isObject(this.selectedOptions)) {
                _.each(this.selectedOptions, function (value) {
                    slotId = value.sector.time_slot ? value.sector.time_slot.uniqueSlotId : 0;
                    timeSlots = this.prepareTimeSlotSummary(timeSlots, value.sector.time_slot);
                    sectorId = value.sector.id;
                    ticket = value;
                    ticket.total = priceUtils.formatPrice(
                        Number(value.ticket.price_info.final_price) * Number(value.qty),
                        this.priceFormat
                    );
                    ticket.exclTaxTotal = priceUtils.formatPrice(
                        Number(value.ticket.price_info.extension_attributes.tax_adjustments.final_price) * Number(value.qty),
                        this.priceFormat
                    );
                    ticket.formattedPrice = value.ticket.price_info.formatted_prices.final_price;
                    if (_.isUndefined(timeSlots[slotId].sectors[sectorId])) {
                        timeSlots[slotId].sectors[sectorId] = {name: value.sector.name, tickets: [ticket]};
                    } else {
                        timeSlots[slotId].sectors[sectorId].tickets = timeSlots[slotId].sectors[sectorId].tickets.concat(ticket)
                    }
                }, this);
            }

            _.each(timeSlots, function (timeSlot) {
                timeSlot.sectors = timeSlot.sectors.filter(Boolean);
            }, this);

            return this._convertToArray(timeSlots);
        },

        /**
         * Retrieve selected ticket and product price
         *
         * @returns {String}
         */
        prepareTimeSlotSummary: function (timeSlots, currentTimeSlot) {
            var slotId = currentTimeSlot ? currentTimeSlot.uniqueSlotId : 0;

            if (!timeSlots[slotId]) {
                timeSlots[slotId] = {};
                timeSlots[slotId].date = currentTimeSlot ? currentTimeSlot.timeSlotDate : false;
                timeSlots[slotId].range = currentTimeSlot ? currentTimeSlot.timeSlotRange : false;
                timeSlots[slotId].sectors = [];
            }

            return timeSlots;
        },

        /**
         * Retrieve selected products by time slots
         *
         * @return {Object}
         */
        getSelectedProductsByTimeSlot: function () {
            var sectors = {}, sectorId, product, timeSlots = {}, slotId;

            if (_.isObject(this.selectedProducts)) {
                _.each(this.selectedProducts, function (value) {
                    slotId = value.sector.time_slot ? value.sector.time_slot.uniqueSlotId : 0;
                    timeSlots = this.prepareTimeSlotSummary(timeSlots, value.sector.time_slot);
                    sectorId = value.sector.id;
                    product = value;
                    product.formattedPrice = priceUtils.formatPrice(value.price, this.priceFormat);
                    product.total = priceUtils.formatPrice(
                        Number(value.price) * Number(value.qty),
                        this.priceFormat
                    );
                    product.exclTaxTotal = priceUtils.formatPrice(
                        Number(value.exclTaxPrice) * Number(value.qty),
                        this.priceFormat
                    );
                    if (_.isUndefined(timeSlots[slotId].sectors[sectorId])) {
                        timeSlots[slotId].sectors[sectorId] = {name: value.sector.name, products: [product]};
                    } else {
                        if (timeSlots[slotId].sectors[sectorId].products) {
                            timeSlots[slotId].sectors[sectorId].products =  timeSlots[slotId].sectors[sectorId].products.concat(product);
                        } else {
                            timeSlots[slotId].sectors[sectorId].products =  [product];
                        }
                    }
                }, this);
            }

            _.each(timeSlots, function (timeSlot) {
                timeSlot.sectors = timeSlot.sectors.filter(Boolean);
            }, this);

            return this._convertToArray(timeSlots);
        },

        /**
         * Retrieve selected ticket and product price
         *
         * @returns {String}
         */
        getTotalSummary: function () {
            var price = 0;

            if (_.isObject(this.selectedOptions)) {
                _.each(this.selectedOptions, function (value) {
                    price += Number(value.ticket.price_info.final_price) * Number(value.qty);
                });
            }

            if (_.isObject(this.selectedProducts)) {
                _.each(this.selectedProducts, function (value) {
                    price += Number(value.price) * Number(value.qty);
                });
            }

            return priceUtils.formatPrice(price, this.priceFormat);
        },

        /**
         * Retrieve selected ticket qty
         *
         * @returns {Number}
         */
        getSelectedTicketQty: function () {
            var qty = 0;

            if (_.isObject(this.selectedOptions)) {
                _.each(this.selectedOptions, function (value) {
                    qty += Number(value.qty);
                });
            }

            return qty;
        },

        /**
         * Retrieve selected product qty
         *
         * @returns {Number}
         */
        getSelectedProductQty: function () {
            var qty = 0;

            if (_.isObject(this.selectedProducts)) {
                _.each(this.selectedProducts, function (value) {
                    qty += Number(value.qty);
                });
            }

            return qty;
        },

        /**
         * Return whether display setting is to display
         * both price including tax and price excluding tax.
         *
         * @return {Boolean}
         */
        isNeedToDisplayTax: function () {
            return this.source.data.displayTaxes === this.displayBothPrices;
        },

        /**
         * Retrieve selected ticket and product total excluding tax
         *
         * @returns {String}
         */
        getTotalSummaryExlcTax: function () {
            var price = 0, exclTaxPrice;

            if (_.isObject(this.selectedOptions)) {
                _.each(this.selectedOptions, function (value) {
                    exclTaxPrice = value.ticket.price_info.extension_attributes.tax_adjustments.final_price;
                    price += Number(exclTaxPrice) * Number(value.qty);
                });
            }

            if (_.isObject(this.selectedProducts)) {
                _.each(this.selectedProducts, function (value) {
                    price += Number(value.exclTaxPrice) * Number(value.qty);
                });
            }

            return priceUtils.formatPrice(price, this.priceFormat);
        },

        /**
         * Convert object to array
         *
         * @param {Object} obj
         * @return {Array}
         * @private
         */
        _convertToArray: function (obj) {
            var arr = [];

            _.each(obj, function(elem){
                arr.push(elem);
            });

            return arr;
        }
    });
});
