define([
    'Magento_Ui/js/modal/modal-component',
    'uiRegistry',
    'underscore'
], function (Modal, registry, _) {
    'use strict';

    return Modal.extend({
        defaults: {
            stepWizardName: '',
            modules: {
                formProvider: '${ $.provider }'
            }
        },

        /**
         * Open modal
         */
        openModal: function () {
            var stepWizard;

            stepWizard = registry.get('index = ' + this.stepWizardName);
            if (!_.isUndefined(stepWizard)) {
                stepWizard.open();
            }

            this._super();
        }
    });
});
