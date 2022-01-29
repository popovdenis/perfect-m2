define([
    'jquery',
    'underscore',
    'Aheadworks_EventTickets/js/product/form/options/sector/product/options-renderer/configurable/abstract-option',
    'Aheadworks_EventTickets/js/product/form/field-name-generator',
    'awEtPriceBox',
    'Magento_Swatches/js/swatch-renderer'
], function ($, _, AbstractOption, fieldNameGenerator) {
    'use strict';

    /**
     * Check if fields is valid
     *
     * @param {Array} items
     * @returns {Boolean}
     */
    function isValidFields(items) {
        var result = true,
            config = {errorElement: 'div'},
            $item;

        _.each(items, function (item) {
            $item = $(item);
            config = $.extend($item.rules(), config);
            if (!$.validator.validateSingleElement(item, config)) {
                result = false;
            }
        });

        return result;
    }

    return AbstractOption.extend({
        isValidFields: true,

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            _.bindAll(
                this,
                'initSwatchesWidgets',
                'afterRenderSwatchInput'
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
            var uidSelector, swatchAttributeInput;

            this._super();

            uidSelector = '#' + this.customScope;
            swatchAttributeInput = this.customScope + '-swatch-input';
            _.extend(this, {
                _uidSelector: uidSelector,
                _swatchUidSelector: uidSelector + ' [data-role=swatch-options]',
                _slyOldPriceSelector: uidSelector + ' .sly-old-price',
                _priceBoxSelector: uidSelector + ' .price-box',
                _swatchAttributeInput: swatchAttributeInput,
                _swatchAttributeInputSelector: '.' + swatchAttributeInput
            });
        },

        /**
         * Init swatch options
         *
         * @returns {Component}
         */
        initOptions: function () {
            $.async(this._swatchUidSelector, this.name, this.initSwatchesWidgets);

            return this;
        },

        /**
         * Initialize swatches widgets
         */
        initSwatchesWidgets: function () {
            var spConfig = this.options.spConfig;

            $(this._priceBoxSelector).priceBox({
                priceConfig: {
                    priceFormat: spConfig.priceFormat,
                    prices: spConfig.prices
                },
                uniqueId: this.customScope
            });

            $(this._swatchUidSelector).SwatchRenderer({
                classes: {
                    attributeInput: this._swatchAttributeInput,
                },
                productId: this.options.productId,
                awEtEntity: true,
                selectorProduct: this._uidSelector + ' .product-item-details',
                slyOldPriceSelector: this._slyOldPriceSelector,
                selectorProductPrice: '.price-box',
                enableControlLabel: false,
                jsonConfig: spConfig,
                jsonSwatchConfig: this.options.jsonSwatchConfig,
                mediaCallback: this.options.mediaCallback
            });

            $.async(
                this._swatchAttributeInputSelector,
                document.getElementById('product_addtocart_form'),
                this.afterRenderSwatchInput
            );
            this._resolveLoaderStatusData([]);
        },

        /**
         * Change input name after render swatch input
         */
        afterRenderSwatchInput: function (node) {
            var $node = $(node),
                name = $node.attr('name'),
                newName = fieldNameGenerator.generate(this.dataScope);

            $node.attr('name', name.replace('super_attribute', newName));
            $node.attr('data-aw-ignore', true);

            $node.on('change', function () {
                isValidFields([$(this)]);
            });
        },

        /**
         * Validate method
         */
        validate: function () {
            this.isValidFields = isValidFields($(this._swatchAttributeInputSelector));
            this.source.set('params.invalid', !this.isValidFields);
        },

        /**
         * Checks if component has error.
         *
         * @returns {Object|null}
         */
        checkInvalid: function () {
            var findItem = null;

            if (!this.isValidFields) {
                _.each($(this._swatchAttributeInputSelector), function (item) {
                    if ($(item).valid() === 0) {
                        findItem = $(item);
                        return true;
                    }
                });
            }

            return findItem;
        }
    });
});
