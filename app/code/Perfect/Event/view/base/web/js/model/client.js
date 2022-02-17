define([
    'ko',
    'uiClass'
], function (ko, Class) {
    'use strict';

    return Class.extend({
        initialize: function () {
            this._super().initObservable();

            return this;
        },

        initObservable: function () {
            return this;
        }
    });
});