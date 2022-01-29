define([
    'Aheadworks_EventTickets/js/ui/form/element/select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            oneTimeValue: '1',
            listens: {
                '${ $.provider }:updateTrigger': 'onDynamicRowUpdate',
            },
        },

        /**
         * @inheritDoc
         */
        onUpdate: function () {
            this.setFieldsVisibility(this.value());
            this._super();
        },

        /**
         * @inheritdoc
         */
        getInitialValue: function () {
            var value = this._super();

            this.setFieldsVisibility(value);
            return value;
        },

        /**
         * Change fields visibility on dynamic rows updates
         */
        onDynamicRowUpdate: function() {
            this.setFieldsVisibility(this.value());
        },

        /**
         * Set price fields visibility
         *
         * @param value
         */
        setFieldsVisibility: function (value) {
            if (value == this.oneTimeValue) {
                this.source.trigger('showFields');
            } else {
                this.source.trigger('hideFields');
            }
        }
    });
});
