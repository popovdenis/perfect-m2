define([
    'Aheadworks_EventTickets/js/product/new/space/steps/abstract-select-in-grid',
    'mage/translate'
], function (Component, $t) {
    'use strict';

    return Component.extend({
        /**
         * Next action
         *
         * @param {Object} wizard
         */
        force: function (wizard) {
            wizard.data.venueId = this.getSelectedValue();
            wizard.data.venueRowData = this.getSelectedRowData(wizard.data.venueId);

            if (!wizard.data.venueId.length) {
                throw new Error($t('Please select venue.'));
            }
        }
    });
});
