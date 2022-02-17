define([
    'ko',
    'uiClass',
    'Perfect_Event/js/model/client'
], function (ko, Class, Client) {
    'use strict';

    return Class.extend({
        initialize: function () {
            this._super().initObservable();

            return this;
        },

        initObservable: function () {
            this.items = ko.observableArray(ko.utils.arrayMap(this.items, function (client) {
                return new Client(client);
            }));

            return this;
        },

        getList: function () {
            return this.items();
        },

        filter: function (callback) {
            return callback(this.items());
        }
    });
});
