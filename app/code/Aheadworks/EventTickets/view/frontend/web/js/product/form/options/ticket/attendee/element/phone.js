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
            this._super().initPhone();
        },

        /**
         * Init field value from customer data
         */
        initPhone: function () {
            var customerInfo = customerData.get('customer');

            if (!this.value() && this.attendeeNumber === 0 && customerInfo().phone) {
                this.value(customerInfo().phone);
            }
        }
    });
});
