define([
    'Aheadworks_EventTickets/js/product/form/options/sector/product/options-renderer/renderer-abstract',
    'uiLayout',
    'mageUtils'
], function (Component, layout, utils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/sector/product/options/configurable',
            modules: {
                option: '${ $.name }.option'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                .resolveOptions();

            return this;
        },

        /**
         * Initializes configurable options
         *
         * @returns {Component} Chainable
         */
        resolveOptions: function () {
            var optionConfig = {
                parent: this.name,
                options: this.options,
                customScope: this.customScope
            };

            if (!this.options.isRenderSwatches) {
                optionConfig = utils.extend({}, this.selectOptionConfig, optionConfig);
            } else {
                optionConfig = utils.extend({}, this.swatchOptionConfig, optionConfig);
            }

            layout([optionConfig]);

            return this;
        }
    });
});
