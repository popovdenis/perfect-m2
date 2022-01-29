define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {

            /**
             * Start update base image process based on event name
             *
             * @param {Array} images
             * @param {jQuery} context
             * @param {Boolean} isInProductView
             * @param {String|undefined} eventName
             */
            updateBaseImage: function (images, context, isInProductView, eventName) {
                if (this.options.awEtEntity === true) {
                    if (images[0] && images[0].thumb) {
                        images[0].img = images[0].thumb;
                    }
                    context = this.element.parents('.product-item-details');
                    isInProductView = false;
                }

                this._super(images, context, isInProductView, eventName);
            },

            /**
             * {@inheritdoc}
             */
            _determineProductData: function () {
                if (this.options.awEtEntity === true) {
                    return {
                        productId: this.options.productId,
                        isInProductView: false
                    };
                }

                return this._super();
            }
        });

        return $.mage.SwatchRenderer;
    }
});
