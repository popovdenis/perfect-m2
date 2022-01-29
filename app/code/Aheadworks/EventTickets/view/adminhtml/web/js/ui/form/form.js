define([
    'Magento_Ui/js/form/form'
], function (Form) {
    'use strict';

    return Form.extend({

        /**
         * @inheritDoc
         */
        hideLoader: function () {
            this.source.trigger('form.loaded');
            return this._super();
        }
    });
});
