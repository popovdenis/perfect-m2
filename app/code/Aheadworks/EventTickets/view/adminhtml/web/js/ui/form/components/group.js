define([
    'Magento_Ui/js/form/components/group'
], function (Group) {
    'use strict';

    return Group.extend({
        defaults: {
            listens: {
                'visible': 'onVisibleChange'
            }
        },

        /**
         * On visible change handler
         *
         * @param {Boolean} visible
         */
        onVisibleChange: function (visible) {
            visible ? this.showChildren() : this.hideChildren();
        },
        
        /**
         * Hide children
         */
        hideChildren: function () {
            this.elems.map('visible', false);
        },

        /**
         * Show children
         */
        showChildren: function () {
            this.elems.map('visible', true);
        }
    });
});
