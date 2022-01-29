define([
    'ko',
    'jquery',
    'underscore',
    'uiElement',
    'Aheadworks_EventTickets/js/product/form/field-name-generator'
], function (ko, $, _, UiElement, fieldNameGenerator) {
    'use strict';

    return UiElement.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/qty-box',
            qty: 0,
            availableQty: -1,
            isEnabled: false
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Ticket} Chainable
         */
        initObservable: function () {
            this._super()
                .track('qty')
                .observe('isEnabled');

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
         * Generate name for field custom field
         *
         * @param {String} field
         * @return {String}
         */
        generateCustomFieldName: function (field) {
            return fieldNameGenerator.generate(this.dataScope, field);
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
            this.trigger('qtyUpdate');

            value = Number(value);
            value = Math.floor(value);

            if (value < 0) {
                value = 0;
            }

            if (this.availableQty !== -1) {
                value = value > this.availableQty ? this.availableQty : value;
            }
            this.qty = value;
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
