define([
    'Magento_Ui/js/form/components/insert-listing',
], function (InsertListing) {
    'use strict';

    return InsertListing.extend({

        /** @inheritdoc */
        render: function () {
            this._super();

            if(this.externalSource()) {
                this.reload();
            }

            if (!this.value().length && this.selections()) {
                this.selections().deselectAll();
            }
            this.updateValue();

            return this;
        },

        /**
         * Initial update of the listing that is rendered completely
         */
        initialUpdateListing: function () {
           if(this.isRendered) {
               this._super();
           }
        },
    });
});
