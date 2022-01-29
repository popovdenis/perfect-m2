define([
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout',
    'mage/translate'
], function (_, uiCollection, utils, layout, $t) {
    'use strict';

    return uiCollection.extend({
        defaults: {
            template: 'Aheadworks_EventTickets/product/form/options/ticket/attendee-options',
            imports: {
                customOptions: '${ $.provider }:data.customOptions'
            },
            listens: {
                customOptions: 'addCustomOptions'
            }
        },

        /**
         * Retrieve label
         *
         * @return {String}
         */
        getLabel: function () {
            var label = $t('Attendee {number}');

            return label.replace('{number}', this.attendeeNumber + 1);
        },

        /**
         * Create custom option instance
         *
         * @returns {AttendeeOptions} Chainable
         */
        addCustomOptions: function () {
            if (Array.isArray(this.customOptions) && this.customOptions.length) {
                this.buildCustomOptions();
            }

            return this;
        },

        /**
         * Add attendee options
         *
         * @returns {AttendeeOptions} Chainable
         */
        buildCustomOptions: function () {
            var option, type, name, attendeeNumber;

            _.each(this.customOptions, function (optionConfig) {
                type = this.customOptionTemplates[optionConfig.type];
                attendeeNumber = this.attendeeNumber;

                if (type &&
                    (
                        this.isAllPersonalOptionEmpty
                        || _.indexOf(this.availableOptionUids, optionConfig.uid) !== -1
                    )
                ) {
                    name = optionConfig.id;
                    option = {
                        label: optionConfig.label,
                        validation: this.prepareValidation(optionConfig),
                        options: optionConfig.options || {},
                        name: name,
                        parent: this.name,
                        attendeeNumber: attendeeNumber,
                        dataScope: attendeeNumber + '.' + name
                    };
                    option = utils.extend({}, type, option);

                    layout([option]);
                }
            }, this);

            return this;
        },

        /**
         * Check if options is set
         * @returns {boolean}
         */
        issetOptions: function () {
            return _.size(this.elems()) > 0;
        },

        /**
         * Prepare validation
         *
         * @param {Object} optionConfig
         * @return {Object}
         */
        prepareValidation: function (optionConfig) {
            var validation = {};

            if (optionConfig.isRequire) {
                validation['required-entry'] = optionConfig.isRequire;
            }

            return validation;
        }
    });
});
