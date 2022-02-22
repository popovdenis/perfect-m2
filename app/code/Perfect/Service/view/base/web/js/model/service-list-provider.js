define([
    'jquery',
    'Perfect_Event/js/model/config'
], function ($, config) {
    'use strict';

    var _getMasterServices = function (masterId, onSuccess) {
        $.ajax({
            url: config.getMasterServicesUrl(),
            data: {form_key: window.FORM_KEY, masterId: masterId},
            type: 'post',
            dataType: 'json',
            showLoader: true,
            beforeSend: function () {
            },
            complete: function () {
            }
        }).done(function (result) {
            if (typeof onSuccess === 'function') {
                onSuccess(result);
            }
        }).fail(function (error) {
            console.log(JSON.stringify(error));
        });
    };

    return function (masterId, onSuccess) {
        _getMasterServices(masterId, onSuccess);
    }
});