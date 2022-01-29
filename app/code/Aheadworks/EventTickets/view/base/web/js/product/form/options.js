define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'Aheadworks_EventTickets/js/product/form/record-counter',
], function (_, uiCollection, utils, layout, recordCounter) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options',
            sectorsTemplate: {
                component: 'Aheadworks_EventTickets/js/product/form/options/sectors',
                parent: '${ $.name }',
                provider: '${ $.provider }'
            },
            slotTemplate: {
                component: 'Aheadworks_EventTickets/js/product/form/options/slot',
                parent: '${ $.name }',
                provider: '${ $.provider }'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            recordCounter.resetSlotIncrement();
            if (!this.source.get('data.isRecurring')) {
                this.addDefaultSectors();
            }

            return this;
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         */
        validate: function () {
            var isValid;

            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');
            isValid = !this.source.get('params.invalid');

            if (!isValid) {
                this.focusInvalid();
            }
            return isValid;
        },

        /**
         * Tries to set focus on first invalid form field.
         *
         * @returns {Object}
         */
        focusInvalid: function () {
            var invalidField = _.find(this.delegate('checkInvalid'));

            if (!_.isUndefined(invalidField)) {
                if (_.isFunction(invalidField.focused)) {
                    invalidField.focused(true);
                } else if (_.isFunction(invalidField.focus)) {
                    invalidField.focus();
                }
            }

            return this;
        },

        /**
         * Add default sectors
         */
        addDefaultSectors() {
            var defaultSectorsConfig = {
                ticketTemplate: this.ticketTemplate,
                productTemplate: this.productTemplate,
                dataScope: 'data',
                can_render_products: this.can_render_products
            };
            defaultSectorsConfig = utils.extend({}, this.sectorsTemplate, defaultSectorsConfig);
            layout([defaultSectorsConfig]);
        },

        /**
         * Add time slot sectors
         *
         * @param {Object} timeSlotConfig
         */
        addTimeSlotSectors(timeSlotConfig) {
            if (!this.isTimeSlotInCollection(timeSlotConfig)) {
                let slotId = recordCounter.getSlotRecordId(),
                    timeSlot = {
                    dataScope: 'data.aw_et_slots.' + slotId,
                    name: 'slot_' + slotId,
                    timeSlotConfig: timeSlotConfig,
                    ticketTemplate: this.ticketTemplate,
                    productTemplate: this.productTemplate,
                    sectorsTemplate: this.sectorsTemplate,
                    can_render_products: this.can_render_products
                };

                timeSlotConfig.uniqueSlotId = slotId;
                timeSlot = utils.extend({}, this.slotTemplate, timeSlot);
                layout([timeSlot]);
            }
        },

        /**
         * Add time slot sectors
         *
         * @param {Object} timeSlotConfig
         */
        removeTimeSlotSectors(timeSlotConfig) {
            _.each(this.elems(), function (elem) {
                if (elem.timeSlotConfig.eventId === timeSlotConfig.eventId) {
                    elem.destroy();
                }
            });
        },

        /**
         * Check if time slot is already in collection
         *
         * @param {Object} timeSlotConfig
         */
        isTimeSlotInCollection(timeSlotConfig) {
            var result = false;

            _.each(this.elems(), function (elem) {
                if (elem.timeSlotConfig.eventId === timeSlotConfig.eventId) {
                    result = true;
                }
            });

            return result;
        }
    });
});
