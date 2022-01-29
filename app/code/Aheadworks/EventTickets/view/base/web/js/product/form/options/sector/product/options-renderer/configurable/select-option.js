define([
    'jquery',
    'underscore',
    'Aheadworks_EventTickets/js/product/form/options/sector/product/options-renderer/configurable/abstract-option',
    'uiLayout',
    'mageUtils',
    'Aheadworks_EventTickets/js/product/form/string-generator',
    'awEtConfigurable',
    'awEtPriceBox'
], function ($, _, AbstractOption, layout, utils, stringGenerator) {
    'use strict';

    return AbstractOption.extend({
        _loadedOptionCounter: 0,

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            _.bindAll(
                this,
                'initConfigurableWidgets',
                'afterRender',
            );

            this._super()
                .initOptions();

            return this;
        },

        /**
         * Initializes regular properties of instance
         *
         * @returns {Component} Chainable
         */
        initConfig: function () {
            var uidSelector;

            this._super();

            uidSelector = '#' + this.customScope;
            _.extend(this, {
                _uidSelector: uidSelector,
                _slyOldPriceSelector: uidSelector + ' .sly-old-price',
                _priceBoxSelector: uidSelector + ' .price-box'
            });
        },

        /**
         * Init select options
         *
         * @returns {Component}
         */
        initOptions: function () {
            this._optionDeps = [];
            this._loadedOptionCounter = 0;

            _.each(this.options.attributes, function (attributeData) {
                this._createFieldComponent(attributeData);
                this._optionDeps.push(attributeData.code);
            }, this);

            $.async('.item-options .configurable-option', this.name, this.afterRender);

            return this;
        },

        /**
         * After render.
         * It is used to check if all options are rendered and initialize widget after.
         */
        afterRender: function () {
            this._loadedOptionCounter++;

            if (this._loadedOptionCounter === this._optionDeps.length) {
                _.delay(this.initConfigurableWidgets, 400);
            }
        },

        /**
         * Initialize configurable widgets
         */
        initConfigurableWidgets: function () {
            var spConfig = this.options.spConfig;

            $(this._priceBoxSelector).priceBox({
                priceConfig: {
                    priceFormat: spConfig.priceFormat,
                    prices: spConfig.prices,
                    uniqueId: this.customScope
                }
            });

            spConfig['containerId'] = this._uidSelector;
            $(this._priceBoxSelector).configurable({
                priceHolderSelector: this._priceBoxSelector,
                mediaGallerySelector: this._uidSelector + ' [data-gallery-role=gallery-placeholder]',
                slyOldPriceSelector: this._slyOldPriceSelector,
                normalPriceLabelSelector: this._uidSelector + ' .normal-price .price-label',
                tierPriceTemplateSelector: this._uidSelector + ' #tier-prices-template',
                tierPriceBlockSelector: this._uidSelector + ' [data-role="tier-price-block"]',
                spConfig: spConfig,
                gallerySwitchStrategy: this.options.gallerySwitchStrategy
            });

            this._resolveLoaderStatusData(this._optionDeps);
        },

        /**
         * Create field component
         *
         * @param {Object} attributeData
         */
        _createFieldComponent: function (attributeData) {
            var attributeFieldConfig = {
                parent: this.name,
                name: attributeData.code,
                attributeId: stringGenerator.generateAlphabeticString(5) + attributeData.id,
                dataScope: attributeData.id,
                label: attributeData.label,
                sortOrder: 0,
                validation: {'required-entry': true},
                caption: 'Choose an Option...',
                options: attributeData.options,
                customScope: this.customScope
            };

            attributeFieldConfig = utils.extend({}, this.attributeFieldConfig, attributeFieldConfig);

            layout([attributeFieldConfig]);
        },
    });
});
