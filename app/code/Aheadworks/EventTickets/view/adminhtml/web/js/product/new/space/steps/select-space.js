define([
    'Aheadworks_EventTickets/js/product/new/space/steps/abstract-select-in-grid',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                spaceProvider: '${ $.spaceProviderName }'
            }
        },

        /**
         * Render step
         *
         * @param {Object} wizard
         */
        render: function (wizard) {
            this.wizard = wizard;
            this.spaceProvider().set(
                'params.wizardFilter',
                {'t': new Date().getTime(), 'venue_id': wizard.data.venueId}
            );
        },

        /**
         * Next action
         *
         * @param {Object} wizard
         */
        force: function (wizard) {
            wizard.data.spaceId = this.getSelectedValue();
            wizard.data.spaceRowData = this.getSelectedRowData(wizard.data.spaceId);

            if (!wizard.data.spaceId.length) {
                throw new Error($t('Please select space.'));
            }
        }
    });
});
