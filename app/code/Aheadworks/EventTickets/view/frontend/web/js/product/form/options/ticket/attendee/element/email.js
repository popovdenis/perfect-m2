define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Customer/js/customer-data'
], function (Abstract, customerData) {
    'use strict';

    return Abstract.extend({
        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super().initEmail();
        },

        /**
         * Init field value from customer data
         */
        initEmail: function () {
            var customerInfo = customerData.get('customer');

            if (!this.value() && this.attendeeNumber === 0 && customerInfo().email) {
                this.value(customerInfo().email);
            }
        }
    });
});
