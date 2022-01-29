define([
    'ko',
    'jquery',
    'underscore',
    'uiComponent',
    'uiRegistry',
], function (ko, $, _, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/ui/filter',
            qty: 1,
            awFullCalendarComponent: {}
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            this.awFullCalendarComponent = registry.get('awFullCalendarComponent');

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns this
         */
        initObservable: function () {
            this._super()
                .track('qty');

            this._qty = ko.pureComputed({
                read: function () {
                    return this.qty;
                },

                /**
                 * Validates input field prior to updating 'qty' property
                 */
                write: function (value) {
                    this._modifyQtyValue(value);
                    this._qty.notifySubscribers(this.qty);
                },

                owner: this
            });

            return this;
        },

        /**
         * Decrease Qty value
         */
        decreaseQty: function () {
            var qty = this.qty;

            qty--;
            this._modifyQtyValue(qty);
            $(this.$qtyInput).change();
        },

        /**
         * Increase Qty value
         */
        increaseQty: function () {
            var qty = this.qty;

            qty++;
            this._modifyQtyValue(qty);
            $(this.$qtyInput).change();
        },

        /**
         * Modify Qty value
         *
         * @param {String|Number} value
         * @private
         */
        _modifyQtyValue: function (value) {
            value = Number(value);

            if (value < 1) {
                value = 1;
            }

            this.qty = value;
            this.awFullCalendarComponent.filterTimeSlots(value);
        },

        /**
         * Handler function which is supposed to be invoked when
         * qty input element has been rendered
         *
         * @param {HTMLInputElement} input
         */
        onQtyInputRender: function (input) {
            this.$qtyInput = input;
        }
    });
});
