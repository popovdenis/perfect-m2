define([
    'ko',
    'uiCollection',
    'mageUtils',
    'Magento_Catalog/js/price-utils',
    'uiLayout',
    'Aheadworks_EventTickets/js/product/form/field-name-generator'
], function (ko, uiCollection, utils, priceUtils, layout, fieldNameGenerator) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/ticket',
            imports: {
                customOptions: '${ $.provider }:data.customOptions',
                priceFormat: '${ $.provider }:data.priceFormat',
                qty: '${ $.qtyBoxConfig.name }:qty'
            },
            exports: {
                selected: '${ $.provider }:params.selectedOptions',
                availableQty: '${ $.qtyBoxConfig.name }:availableQty'
            },
            listens: {
                qty: 'onQtyChange updateSelected',
                '${ $.qtyBoxConfig.name }:qtyUpdate': 'onQtyUpdate',
                '${ $.provider }:enableQtyInput': 'enableQtyInput',
            },
            qtyBoxConfig: {
                component: 'Aheadworks_EventTickets/js/product/form/options/qty-box',
                name: '${ $.name }_qtyBox'
            },
            modules: {
                parentComponent: '${ $.parentName }',
                qtyBox: '${ $.qtyBoxConfig.name }'
            }
        },

        /**
         * Enable qty input
         */
        enableQtyInput: function() {
            this.qtyBox().isEnabled(true);
        },

        /**
         * Initializes filters component
         *
         * @returns {Ticket} Chainable
         */
        initialize: function () {
            this._super()
                .initQtyBox();

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Ticket} Chainable
         */
        initObservable: function () {
            this._super()
                .track('qty');

            return this;
        },

        /**
         * Initializes qty-box component.
         *
         * @returns {Ticket} Chainable
         */
        initQtyBox: function () {
            var qtyBoxConfig = {
                dataScope: this.dataScope,
                qty: this.ticket.qty,
                isEnabled: this.sector.is_salable,
                availableQty: this.parentComponent().getAvailableTicketsQty(this.recordId)
            };

            qtyBoxConfig = utils.extend({}, this.qtyBoxConfig, qtyBoxConfig);
            layout([qtyBoxConfig]);

            return this;
        },

        /**
         * Generate name for field custom field
         *
         * @param {String} field
         * @return {String}
         */
        generateCustomFieldName: function (field) {
            return fieldNameGenerator.generate(this.dataScope, field);
        },

        /**
         * Change qty handler
         *
         * @param {Number} qty
         */
        onQtyChange: function (qty) {
            this.renderAttendeeOptions(qty);
        },

        /**
         * Update qty handler
         */
        onQtyUpdate: function () {
            this.set('availableQty', this.parentComponent().getAvailableTicketsQty(this.recordId));
        },

        /**
         * Update selected value
         *
         * @param {Number} qty
         */
        updateSelected: function (qty) {
            var selectedParam = {},
                selectedOptions =  utils.copy(this.source.params.selectedOptions),
                slotId = this.sector.time_slot ? this.sector.time_slot.uniqueSlotId : 'default',
                params;

            if (qty === 0) {
                params = selectedOptions || {};
                delete params[slotId + '-' + this.recordId];
            } else {
                selectedParam[slotId + '-' + this.recordId] = {
                    qty: qty,
                    ticket: {
                        price_info: this.ticket.price_info,
                        ticket_type: this.ticket.ticket_type
                    },
                    sector: {
                        id: this.sector.id,
                        name: this.sector.name,
                        time_slot: this.sector.time_slot
                    }
                };
                params = utils.extend({}, selectedOptions, selectedParam);
            }
            this.set('selected', params);
        },

        /**
         * Retrieve total price
         *
         * @return {String}
         */
        getTotalPrice: function () {
            var price = Number(this.ticket.price_info.final_price) * this.qty;

            return priceUtils.formatPrice(price, this.priceFormat);
        },

        /**
         * Create custom option instance
         *
         * @param {Number} qty
         * @returns {Ticket} Chainable
         */
        renderAttendeeOptions: function (qty) {
            var attendeeCount = this.getRegion('attendee')().length,
                newAttendeeCount, attendeeNumber;

            if (!Array.isArray(this.customOptions)
                || (Array.isArray(this.customOptions) && !this.customOptions.length)
            ) {
                return this;
            }

            if (attendeeCount < qty) {
                newAttendeeCount = (qty - attendeeCount) + attendeeCount;
                for (attendeeNumber = attendeeCount; attendeeNumber < newAttendeeCount; attendeeNumber++) {
                    this.addAttendeeOptions(attendeeNumber);
                }
            } else if (attendeeCount > 0 && attendeeCount > qty) {
                newAttendeeCount = attendeeCount - (attendeeCount - qty);
                for (attendeeNumber = attendeeCount; attendeeNumber > newAttendeeCount; attendeeNumber--) {
                    this.deleteAttendeeOptions(attendeeNumber - 1);
                }
            }

            return this;
        },

        /**
         * Add attendee options
         *
         * @param {Number} attendeeNumber
         * @returns {Ticket} Chainable
         */
        addAttendeeOptions: function (attendeeNumber) {
            var attendee;

            attendee = {
                attendeeNumber: attendeeNumber,
                name: 'attendee_' + attendeeNumber,
                dataScope: 'attendee',
                parent: this.name,
                displayArea: 'attendee',
                availableOptionUids: this.ticket.available_option_uids,
                isAllPersonalOptionEmpty: this.ticket.is_all_personal_option_empty
            };
            attendee = utils.extend({}, this.attendeeTemplate, attendee);
            layout([attendee]);

            return this;
        },

        /**
         * Retrieve ticket data as array for rendering price
         *
         * @returns {Object}
         */
        getDataForRenderingPrice: function () {
            var ticketAsProduct = this.ticket;

            ticketAsProduct.type = 'simple';
            ticketAsProduct.is_salable = true;
            return [ticketAsProduct];
        },

        /**
         * Delete attendee options
         *
         * @param {Number} attendeeNumber
         * @return {Ticket} Chainable
         */
        deleteAttendeeOptions: function (attendeeNumber) {
            var recordInstance;

            recordInstance = _.find(this.getRegion('attendee')(), function (elem) {
                return elem.attendeeNumber === attendeeNumber;
            });
            if (recordInstance) {
                recordInstance.destroy();
            }

            return this;
        },

        /**
         * @inheritdoc
         */
        destroy: function () {
            this.updateSelected(0);
            this._super();
        }
    });
});
