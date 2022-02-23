define([
    'uiClass',
    'jquery',
    'Perfect_Service/js/model/service-provider',
    'underscore',
    'Perfect_Event/js/storage'
], function (Class, $, serviceProvider, _, storage) {
    'use strict';

    return Class.extend({
        getServices: function (masterId, onSuccess) {
            if (storage.getMasterServices(masterId).length) {
                onSuccess(storage.getMasterServices(masterId));
            }
            if (!storage.getMasterServices(masterId).length) {
                serviceProvider.getServices(masterId)
                    .then(function (services) {
                        console.log('save services in storage');
                        console.log('display services...');
                        storage.masterServices.push({masterId: masterId, services: services});
                        onSuccess(storage.getMasterServices(masterId));
                    });
            }
        }
    })
});