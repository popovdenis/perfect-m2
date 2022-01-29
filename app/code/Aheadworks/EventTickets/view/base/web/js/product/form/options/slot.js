define([
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'Aheadworks_EventTickets/js/product/form/field-name-generator',
    'uiRegistry'
], function (uiCollection, utils, layout, fieldNameGenerator, registry) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/slot',
            calendar: registry.get('awFullCalendarComponent'),
            isAllowed: false
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .addSectors();
            this.isAllowed = this.calendar.getData('isTimeSlotMultiSelectionAllowed');

            return this;
        },

        /**
         * Generate name for field
         *
         * @param {String} field
         * @return {String}
         */
        generateCustomFieldName: function (field) {
            return fieldNameGenerator.generate('data.aw_et_slots.' + this.timeSlotConfig.uniqueSlotId, field);
        },

        /**
         * Create sector instance
         *
         * @returns {Slot} Chainable
         */
        addSectors: function () {
            var sector = {
                displayArea: 'sectors',
                timeSlotConfig: this.timeSlotConfig,
                ticketTemplate: this.ticketTemplate,
                productTemplate: this.productTemplate,
                can_render_products: this.can_render_products,
                parent: this.name
            };

            sector = utils.extend({}, this.sectorsTemplate, sector);
            layout([sector]);

            return this;
        },

        /**
         * Remove Slot
         *
         * @returns {Slot} Chainable
         */
        removeSlot: function (el) {
            this.calendar.calendarEventDoubleClickHandler(el.timeSlotConfig.eventId);

            return this;
        }
    });
});
