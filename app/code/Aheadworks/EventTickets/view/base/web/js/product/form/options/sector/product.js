define([
    'ko',
    'jquery',
    'underscore',
    'uiComponent',
    'uiLayout',
    'mageUtils',
    'Magento_Catalog/js/price-utils',
    'Aheadworks_EventTickets/js/product/form/field-name-generator'
], function (ko, $, _, Component, layout, utils, priceUtils, fieldNameGenerator) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/sector/product',
            qty: 0,
            price: 0,
            qtyBoxConfig: {
                component: 'Aheadworks_EventTickets/js/product/form/options/qty-box',
                name: '${ $.name }_qtyBox'
            },
            imports: {
                qty: '${ $.qtyBoxConfig.name }:qty',
                price: '${ $.provider}:${ $.dataScope }.price'
            },
            exports: {
                qty: '${ $.provider }:${ $.dataScope }.qty',
                selected: '${ $.provider }:params.selectedProducts',
            },
            listens: {
                '${ $.provider }:data.validate': 'validate',
                loaderStatusData: 'updateLoaderStatus',
                isLoading: 'onProductRender',
                qty: 'qtyUpdate',
                price: 'getPriceFormDataSource'
            },
            modules: {
                sectorComponent: '${ $.parentName }',
                qtyBox: '${ $.qtyBoxConfig.name }'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .createOptions()
                .initQtyBox()
                .initEventListeners();

            return this;
        },

        /**
         * Initializes qty-box component.
         *
         * @returns {Product} Chainable
         */
        initQtyBox: function () {
            var qtyBoxConfig = {
                dataScope: this.dataScope,
                qty: this.product.qty,
                isEnabled: this.product.is_salable
            };

            qtyBoxConfig = utils.extend({}, this.qtyBoxConfig, qtyBoxConfig);
            layout([qtyBoxConfig]);

            return this;
        },

        /**
         * Initializes event listeners
         *
         * @returns {Product} Chainable
         */
        initEventListeners: function () {
            if (_.indexOf(this.typesUsingDataPriceAttr, this.product.type) !== -1) {
                $(document).on('awEtPriceUpdated_' + this.getUid(), function () {
                    this.getPriceFromDataAttribute();
                }.bind(this));
            }

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
         * Initializes observable properties
         *
         * @returns {Product} Chainable
         */
        initObservable: function () {
            this._super()
                .observe({
                    isLoading: true,
                    isOptionVisible: false,
                    loaderStatusData: {}
                });

            return this;
        },

        /**
         * Update selected value
         */
        updateSelected: function () {
            var selectedParam = {},
                selectedProducts = utils.copy(this.source.params.selectedProducts),
                slotId = this.sector.time_slot ? this.sector.time_slot.uniqueSlotId : 'default',
                params;

            if (this.qty === 0) {
                params = selectedProducts || {};
                delete params[slotId + '-' + this.recordId];
            } else {
                selectedParam[slotId + '-' + this.recordId] = {
                    qty: this.qty,
                    price: this.price,
                    exclTaxPrice: this.exclTaxPrice,
                    sector: {
                        id: this.sector.id,
                        name: this.sector.name,
                        time_slot: this.sector.time_slot
                    },
                    product: {
                        name: this.product.name
                    }
                };
                params = utils.extend({}, selectedProducts, selectedParam);
            }
            this.set('selected', params);
        },

        /**
         * Update price
         *
         * @param {Number} price
         */
        priceUpdate: function (price) {
            this.price = price;
            this.updateSelected();
        },

        /**
         * Update quantity
         *
         * @param {Number} qty
         */
        qtyUpdate: function (qty) {
            this.qty = qty;
            this.updateSelected();
        },

        /**
         * Get price form data attribute of price box
         */
        getPriceFromDataAttribute: function () {
            var finalPrice = this.retrievePriceFromDataAttrByType('finalPrice'),
                oldPrice = this.retrievePriceFromDataAttrByType('oldPrice'),
                exclTaxPrice = this.retrievePriceFromDataAttrByType('basePrice'),
                resultPrice = finalPrice || oldPrice;

            if (!_.isUndefined(exclTaxPrice)) {
                this.exclTaxPrice = Number(exclTaxPrice);
            }

            if (!_.isUndefined(resultPrice)) {
                this.priceUpdate(Number(resultPrice));
                this.updatePriceBox(finalPrice, oldPrice);
            }
        },

        /**
         * Update price elements inside price box
         *
         * @param {String} finalPrice
         * @param {String} oldPrice
         */
        updatePriceBox: function(finalPrice, oldPrice) {
            var elemContainer = $('#' + this.getUid());

            if (oldPrice !== finalPrice) {
                if ($('.special-price', elemContainer).hasClass('hide-price')) {
                    $('.special-price', elemContainer).toggleClass('hide-price show-price');
                }
                $('.regular-price', elemContainer).addClass('old-price sly-old-price');
            } else {
                if ($('.special-price', elemContainer).hasClass('show-price')
                    && !$('.special-price', elemContainer).hasClass('initial')
                ) {
                    $('.special-price', elemContainer).toggleClass('show-price hide-price');
                }
                if ($('.special-price', elemContainer).hasClass('show-price')
                    && $('.special-price', elemContainer).hasClass('initial')
                ) {
                    $('.regular-price', elemContainer).hide();
                }
                $('.regular-price', elemContainer).removeClass('old-price sly-old-price');
            }
        },

        /**
         * Generate name for field custom field
         *
         * @param {String} type
         * @return {String}
         */
        retrievePriceFromDataAttrByType: function(type) {
            var elemContainer = $('#' + this.getUid());

            return $('[data-price-type="' + type + '"]', elemContainer).attr('data-price-amount');
        },

        /**
         * Get price form data source
         */
        getPriceFormDataSource: function () {
            var resultPrice = this.source.get(this.dataScope + '.price'),
                exclTaxPrice = this.source.get(this.dataScope + '.exclTaxPrice');

            if (!_.isUndefined(exclTaxPrice)) {
                this.exclTaxPrice = Number(exclTaxPrice);
            }

            if (!_.isUndefined(resultPrice)) {
                this.priceUpdate(Number(resultPrice));
            }
        },

        /**
         * Update loader status
         */
        updateLoaderStatus: function() {
            var loaderData = this.loaderStatusData(), loaderStatus;

            if (loaderData.optionDeps) {
                loaderStatus = {
                    parent: this.name,
                    name: 'loaderStatus',
                    deps: []
                };

                _.each(this.loaderStatusConfig.defaultDeps, function (defaultDep) {
                    loaderStatus.deps.push(this.name + '.' + defaultDep);
                }.bind(this));

                if (loaderData.optionDeps.length) {
                    loaderStatus.deps.push(this.name + '.options.option');
                    _.each(loaderData.optionDeps, function (optionDep) {
                        loaderStatus.deps.push(this.name + '.options.option.' + optionDep);
                    }.bind(this));
                }

                loaderStatus = utils.extend({}, this.loaderStatusConfig, loaderStatus);
                layout([loaderStatus]);
            }
        },

        /**
         * Retrieve component unique id
         *
         * @returns {String}
         */
        getUid: function () {
            return this.product.key;
        },

        /**
         * On product render handler
         */
        onProductRender: function() {
            if (!this.isLoading()) {
                this.sectorComponent('addNextProduct');
            }
        },

        /**
         * Validate
         */
        validate: function () {
            if (this.qty) {
                this.source.trigger(this.getUid() + '.data.validate');
                this._checkOptionValidation();
            }
        },

        /**
         * Create options for product
         *
         * @returns {Product}
         */
        createOptions: function () {
            var optionsData = this.product.option,
                optionDeps = [];

            if (_.size(optionsData) > 0 && this.product.is_salable) {
                optionsData.productId = this.product.id;
                this._createOptionsRendererComponent(this.product.type, optionsData);
            } else {
                this.loaderStatusData({
                    'optionDeps': optionDeps
                });
            }
            return this;
        },

        /**
         * Retrieve product data as array for rendering price
         *
         * @returns {Object}
         */
        getDataForRenderingPrice: function () {
            return [this.product];
        },

        /**
         * Create options renderer component
         *
         * @param {String} type
         * @param {Object} options
         */
        _createOptionsRendererComponent: function (type, options) {
            var optionsConfig = {
                parent: this.name,
                name: 'options',
                dataScope: 'super_attribute',
                displayArea: 'options',
                options: options,
                customScope: this.getUid(),
                exports: {
                    'loaderStatusData': this.name + ':loaderStatusData'
                }
            };

            optionsConfig = utils.extend({}, this.optionsConfig, optionsConfig);
            optionsConfig = type !== 'default' && !_.isUndefined(this.rendererList[type])
                ? utils.extend({}, optionsConfig, this.rendererList[type])
                : optionsConfig;

            layout([optionsConfig]);
        },

        /**
         * Check option validation and expand section if something is not correct
         */
        _checkOptionValidation: function() {
            var optionSection = this.getRegion('options')();

            _.each(optionSection, function (option) {
                _.each(option.option().elems(), function (elem) {
                    if (elem.error()) {
                        this.isOptionVisible(true);
                    }
                }.bind(this));
            }.bind(this));
        },

        /**
         * @inheritdoc
         */
        destroy: function () {
            this.qty = 0;
            this.updateSelected(0);
            this._super();
        }
    });
});
