define([
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'mageUtils'
], function (_, Abstract, utils) {
    'use strict';

    return Abstract.extend({

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();

            if (_.isEmpty(this.value())) {
                this.value(utils.uniqueid());
            }

            return this;
        }
    });
});
