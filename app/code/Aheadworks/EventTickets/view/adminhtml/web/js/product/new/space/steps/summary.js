define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'mage/translate'
], function ($, Component, alert, _, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            notificationMessage: {
                text: null,
                error: null
            },
            modules: {
                sectorsProvider: '${ $.sectorsProviderName }',
                modalComponent: '${ $.modalComponent }',
                formProvider: '${ $.formProvider }',
                spaceConfiguration: '${ $.spaceConfigurationComponent }'
            },
            venueInfoFields: [],
            spaceInfoFields: [],
            venueInfo: [],
            spaceInfo: []
        },
        nextLabelText: $t('Save Space Configuration'),

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super().observe(['venueInfo', 'spaceInfo']);

            return this;
        },

        /**
         * Render step
         *
         * @param {Object} wizard
         */
        render: function (wizard) {
            this.wizard = wizard;
            this.venueInfo(this.getVenueInfo());
            this.spaceInfo(this.getSpaceInfo());
            this.sectorsProvider().set(
                'params.wizardFilter',
                {'t': new Date().getTime(), 'space_id': wizard.data.spaceId}
            );
        },

        /**
         * Next action
         *
         * @param {Object} wizard
         */
        force: function (wizard) {
            this._loadSectorConfigData(wizard);
        },

        /**
         * Back action
         */
        back: function () {

        },

        /**
         * Retrieve venue info
         *
         * @return {Array}
         */
        getVenueInfo: function () {
            return this.wizard.data.venueRowData;
        },

        /**
         * Retrieve value for venue info field
         *
         * @param {String} fieldName
         * @returns {String}
         */
        getVenueFieldValue: function (fieldName) {
            return this._getValueFromRowData(this.venueInfo(), fieldName);
        },

        /**
         * Retrieve space info
         *
         * @return {Array}
         */
        getSpaceInfo: function () {
            return this.wizard.data.spaceRowData;
        },

        /**
         * Retrieve value for space info field
         *
         * @param {String} fieldName
         * @returns {String}
         */
        getSpaceFieldValue: function (fieldName) {
            return this._getValueFromRowData(this.spaceInfo(), fieldName);
        },

        /**
         * Retrieve value from row data
         *
         * @param {Array} rowData
         * @param {String} fieldName
         * @return {String}
         */
        _getValueFromRowData: function (rowData, fieldName) {
            return _.find(rowData, function (value, index) {
                return index === fieldName;
            }, this);
        },

        /**
         * Load sector config data
         * @private
         */
        _loadSectorConfigData: function (wizard) {
            var self = this;

            $('body').trigger('processStart');
            $.ajax({
                url: this.loadSectorConfigUrl,
                type: 'POST',
                dataType: 'json',
                data: {space_id: wizard.data.spaceId},

                /**
                 * Success callback
                 * @param {Object} response
                 * @returns {Boolean}
                 */
                success: function(response) {
                    if (response.error) {
                        self.onError(response.message);
                        return true;
                    } else {
                        self.onSuccess(response, wizard);
                    }
                    return false;
                },

                /**
                 * Complete callback
                 */
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        },

        /**
         * Ajax request error handler
         *
         * @param {String} errorMessage
         */
        onError: function (errorMessage) {
            alert({content: errorMessage});
        },

        /**
         * Ajax request success handler
         *
         * @param {Object} response
         * @param {Object} wizard
         */
        onSuccess: function (response, wizard) {
            this.formProvider().set('data.product.aw_et_venue_id', wizard.data.venueId);
            this.formProvider().set('data.product.aw_et_space_id', wizard.data.spaceId);
            this.formProvider().set('data.product.aw_et_sector_config', response.sectorConfigData);
            this.spaceConfiguration().reload();
            this.modalComponent().closeModal();
        }
    });
});
