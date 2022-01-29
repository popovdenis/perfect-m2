define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'Aheadworks_EventTickets/js/product/form/record-counter'
], function (_, uiCollection, utils, layout, recordCounter) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/sector',
            modules: {
                parentComponent: '${ $.parentName }'
            },
            visible: true
        },
        _productIndex: 0,

        /**
         * @inheritDoc
         */
        initObservable: function() {
            this._super()
                .track({
                    visible: true
                });

            return this;
        },

        /**
         * Initializes filters component
         *
         * @returns {Sector} Chainable
         */
        initialize: function () {
            this._super()
                .addTickets()
                .addProducts();

            return this;
        },

        /**
         * Create tickets instance
         *
         * @returns {Sector} Chainable
         */
        addTickets: function () {
            if (Array.isArray(this.sector.tickets) && this.sector.tickets.length) {
                this.buildTickets();
            }

            return this;
        },

        /**
         * Create related products instance
         *
         * @returns {Sector} Chainable
         */
        addProducts: function () {
            if (this.parentComponent().can_render_products
                && Array.isArray(this.sector.additional_products)
                && this.sector.additional_products.length
                && !this.sector.is_configure_page
            ) {
                this._productIndex = 0;
                this.addNextProduct();
            }

            return this;
        },

        /**
         * Add next product if possible
         */
        addNextProduct: function() {
            if (this.sector.additional_products[this._productIndex]) {
                this._addProduct(this.sector.additional_products[this._productIndex], this._productIndex);
                this._productIndex++;
            }
        },

        /**
         * Create ticket components
         *
         * @returns {Sector} Chainable
         */
        buildTickets: function () {
            var ticket, recordId;

            _.each(this.sector.tickets, function (ticketConfig) {
                recordId = this.getRecordId();
                ticket = {
                    name: 'ticket_type_' + ticketConfig.ticket_type.id,
                    displayArea: 'tickets',
                    dataScope: 'aw_et_tickets.' + String(recordId),
                    recordId: recordId,
                    ticket: ticketConfig,
                    sector: this.sector,
                    parent: this.name
                };
                ticket = utils.extend({}, this.ticketTemplate, ticket);
                layout([ticket]);
            }, this);

            return this;
        },

        /**
         * Create product instance
         *
         * @param {Object} product
         * @param {Number} index
         * @returns {Sector} Chainable
         */
        _addProduct: function (product, index) {
            var productTemplate = {
                name: 'product_' + product.id,
                displayArea: 'products',
                dataScope: 'aw_et_products.' + this.sector.id + '_' + index,
                product: product,
                recordId: recordCounter.getProductRecordId(),
                sector: this.sector,
                parent: this.name
            };

            productTemplate = utils.extend({}, this.productTemplate, productTemplate);
            layout([productTemplate]);

            return this;
        },

        /**
         * Retrieve record id
         *
         * @return {Number}
         */
        getRecordId: function () {
            return recordCounter.getRecordId();
        },

        /**
         * Retrieve available tickets qty
         *
         * @return {Number}
         */
        getAvailableTicketsQty: function (recordId) {
            return this.sector.qty_available - this.calculateUsedTicketsQty(recordId);
        },

        /**
         * Calculate used tickets qty
         */
        calculateUsedTicketsQty: function (recordId) {
            var qty = 0;

            _.each(this.elems(), function (elem) {
                if (elem.recordId != recordId && elem.displayArea == 'tickets') {
                    qty += elem.qty;
                }
            });

            return qty;
        }
    });
});
