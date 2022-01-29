define([
    'ko',
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/ui-select'
], function (ko, $, _, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            selected: [],
            imports: {
                personalOptions: '${ $.provider }:data.product.aw_et_personal_options'
            },
            listens: {
                personalOptions: 'updateTicketTypeOptions'
            }
        },

        /**
         * Update ticket type options after personal options updated
         */
        updateTicketTypeOptions: function () {
            var self = this, options = [], option, title, value;

            _.each(this.personalOptions, function (personalOption, index) {
                if (personalOption['uid']
                    && personalOption['apply_to_all_ticket_types']
                    && personalOption['labels'] && personalOption['labels'][0]
                ) {
                    title = personalOption['labels'][0]['title'];
                    value = personalOption['uid'];

                    if (title) {
                        option = {'value': value, 'label': title, 'index': index};
                        options.push(option);
                        if (personalOption['apply_to_all_ticket_types'] === "1"
                            && !self.isSelected(option.value)
                        ) {
                            _.delay($.proxy(self.toggleOptionSelected, self), 400, option);
                        }
                    }
                }
            });
            this.options(options);
            this.cacheOptions.tree = options;
            this.cacheOptions.plain = options;
            this.updateValues();
        },

        /**
         * {@inheritdoc}
         */
        isHovered: function (data) {
            var element = this.hoveredElement,
                elementData;

            if (!element) {
                return false;
            }

            elementData = ko.dataFor(this.hoveredElement);

            if (!elementData) {
                return false;
            }

            return data.value === elementData.value;
        },

        /**
         * {@inheritdoc}
         */
        toggleOptionSelected: function (data) {
            var self = this,
                value = data.value,
                isSelectedBefore = this.isSelected(value),
                isSelectedAfter;

            this._super(data);

            isSelectedAfter = this.isSelected(value);

            if (isSelectedBefore && !isSelectedAfter
                && this.personalOptions[data.index]['apply_to_all_ticket_types'] === "1"
            ) {
                self._uncheckApplyToAllTicketTypes(data);
            }
        },

        /**
         * {@inheritdoc}
         */
        removeSelected: function (value, data, event) {
            this._super(value, data, event);
            this._uncheckApplyToAllTicketTypes(data);
        },

        /**
         * Update values if some options delete
         */
        updateValues: function () {
            var self = this,
                valuesInOptions = [];

            _.each(this.value(), function (value) {
                if (_.findWhere(self.options(), {value: value})) {
                    valuesInOptions.push(value);
                }
            });

            this.value(valuesInOptions);
        },

        /**
         * Uncheck apply to all ticket types checkbox
         *
         * @param {Array} data
         */
        _uncheckApplyToAllTicketTypes: function (data) {
            this.source.set(
                'data.product.aw_et_personal_options.' + data.index + '.apply_to_all_ticket_types',
                '0'
            );
        }
    });
});
