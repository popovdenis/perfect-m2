define([
    'jquery',
    'configurable'
], function ($) {
    'use strict';

    $.widget('awEt.configurable', $.mage.configurable, {

        /**
         * {@inheritdoc}
         */
        _configureForValues: function () {
            if (this.options.values) {
                this.options.settings.each($.proxy(function (index, element) {
                    var attributeId = element.attributeId,
                        value = this.options.values[attributeId] || '';

                    element.value = value;
                    if (value) {
                        $(element).trigger('change');
                        $(element).prop('disabled', true);
                    }
                    this._configureElement(element);
                }, this));
            }
        }
    });

    return $.awEt.configurable;
});
