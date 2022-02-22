define([
    'uiClass',
    'jquery',
    'Perfect_Service/js/model/service-provider',
    'underscore',
    'Perfect_Event/js/storage'
], function (Class, $, serviceProvider, _, storage) {
    'use strict';

    return Class.extend({
        getServices: function (masterId) {
            var self = this;
            serviceProvider.getServices(masterId)
                .then(function () {
                    // save services in storage
                    // display services
                    console.log('save services in storage');
                    console.log('display services...');
                });
        }
    })
});