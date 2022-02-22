define([
    'Perfect_Service/js/model/service-list-provider'
], function (servicesList) {
    return {
        getServices: function (masterId) {
            return new Promise(function(resolve) {
                servicesList(masterId, function (services) {
                    resolve(services);
                });
            });
        }
    };
});