define([
    'Magento_Ui/js/dynamic-rows/action-delete',
    'Magento_Ui/js/modal/confirm'
], function (Delete, confirm) {
    'use strict';

    return Delete.extend({
        defaults: {
            confirmTitle: '',
            confirmContent: ''
        },

        /**
         * @inheritDoc
         */
        deleteRecord: function (index, id) {
            let self = this,
                rowData = this.source.get(this.dataScope);

            if (rowData && rowData.id) {
                confirm({
                    title: this.confirmTitle,
                    content: this.confirmContent,
                    actions: {
                        /**
                         * @inheritDoc
                         */
                        confirm: function () {
                            self.bubble('deleteRecord', index, id);
                        }
                    }
                });
            } else {
                this._super(index, id);
            }
        }
    });
});
