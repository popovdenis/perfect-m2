define([
    'ko',
    'uiClass',
    'Perfect_Event/js/model/service'
], function (ko, Class, Service) {
    'use strict';

    return Class.extend({
        initialize: function () {
            this._super().initObservable();

            return this;
        },

        initObservable: function () {
            this.items = ko.observableArray(ko.utils.arrayMap(this.items, function (service) {
                return new Service(service);
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
