define([
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'mage/translate',
    'Aheadworks_EventTickets/js/product/form/record-counter'
], function (uiCollection, utils, layout, $t, recordCounter) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/sectors',
            sectorTemplate: {
                component: 'Aheadworks_EventTickets/js/product/form/options/sector',
                parent: '${ $.name }',
                provider: '${ $.provider }'
            },
            productTemplate: {
                component: 'Aheadworks_EventTickets/js/product/form/options/sector/product',
                rendererList: [],
            },
            ticketTemplate: {
                component: 'Aheadworks_EventTickets/js/product/form/options/ticket',
                provider: '${ $.provider }',
                attendeeTemplate: {
                    component: 'Aheadworks_EventTickets/js/product/form/options/ticket/attendee-options',
                    provider: '${ $.provider }',
                    customOptionTemplates: {
                        name: {
                            component: 'Aheadworks_EventTickets/js/product/form/options/ticket/attendee/element/name',
                            template: 'ui/form/field',
                            provider: '${ $.provider }'
                        },
                        email: {
                            component: 'Aheadworks_EventTickets/js/product/form/options/ticket/attendee/element/email',
                            template: 'ui/form/field',
                            provider: '${ $.provider }',
                            validation: {'validate-email': true}
                        },
                        phone_number: {
                            component: 'Aheadworks_EventTickets/js/product/form/options/ticket/attendee/element/phone',
                            template: 'ui/form/field',
                            provider: '${ $.provider }'
                        },
                        field: {
                            component: 'Magento_Ui/js/form/element/abstract',
                            template: 'ui/form/field',
                            provider: '${ $.provider }'
                        },
                        dropdown: {
                            component: 'Magento_Ui/js/form/element/select',
                            template: 'ui/form/field',
                            provider: '${ $.provider }',
                            caption: $t('-- Please Select --')
                        },
                        date: {
                            component: 'Magento_Ui/js/form/element/date',
                            template: 'ui/form/field',
                            provider: '${ $.provider }',
                            storeTimeZone: 'UTC',
                            options: {
                                'dateFormat' : 'MM/dd/y'
                            }
                        }
                    }
                }
            },
            imports: {
                sectorConfig: '${ $.provider }:data.sectorConfig'
            },
            listens: {
                sectorConfig: 'resetIncrement onSectorUpdate'
            }
        },

        /**
         * Initializes observable properties
         *
         * @returns {Options} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    sectorConfig: []
                });

            return this;
        },


        /**
         * Create sector instance
         *
         * @param {Object} sectorConfig
         * @returns {Sectors} Chainable
         */
        addSector: function (sectorConfig) {
            var sector = {
                name: 'sector_' + sectorConfig.id,
                sector: sectorConfig,
                ticketTemplate: this.ticketTemplate,
                productTemplate: this.productTemplate
            };

            if (this.timeSlotConfig && typeof (this.timeSlotConfig.sectorQty[sectorConfig.id]) !== undefined) {
                sector.sector.qty_available = this.timeSlotConfig.sectorQty[sectorConfig.id];
                sector.sector.time_slot = this.timeSlotConfig;
                if (sector.sector.qty_available <= 0) {
                    return this;
                } else {
                    sector.sector.is_salable = true;
                }
            }

            sector = utils.extend({}, this.sectorTemplate, sector);
            layout([sector]);

            return this;
        },

        /**
         * Listener of the sectors provider children array changes
         *
         * @param {Array} sectors
         */
        onSectorUpdate: function (sectors) {
            sectors.forEach(this.addSector, this);
        },

        /**
         * Reset increment
         */
        resetIncrement: function () {
            recordCounter.resetIncrement();
        },
    });
});
