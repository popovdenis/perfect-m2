define([], function () {
    'use strict';

    return {
        _productIncrement: null,
        _slotIncrement: null,
        _increment: null,

        /**
         * Reset increment
         */
        resetIncrement: function () {
            this._increment = null;
        },

        /**
         * Reset product increment
         */
        resetProductIncrement: function () {
            this._productIncrement = null;
        },

        /**
         * Reset slot increment
         */
        resetSlotIncrement: function () {
            this._slotIncrement = null;
        },

        /**
         * Retrieve record id
         *
         * @return {Number}
         */
        getRecordId: function () {
            return this._increment = this._increment === null ? 0 : this._increment + 1;
        },

        /**
         * Retrieve product record id
         *
         * @return {Number}
         */
        getProductRecordId: function () {
            return this._productIncrement = this._productIncrement === null ? 0 : this._productIncrement + 1;
        },

        /**
         * Retrieve slot record id
         *
         * @return {Number}
         */
        getSlotRecordId: function () {
            return this._slotIncrement = this._slotIncrement === null ? 0 : this._slotIncrement + 1;
        }
    };
});
