define([
    'jquery',
    'timetableConfig'
], function ($, config) {
    'use strict';

    var _saveEvent = function (event, onSuccess) {
        $.ajax({
            url: config.getSaveEventUrl(),
            data: {form_key: window.FORM_KEY, event: event},
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

    return function (data, onSuccess) {
        _saveEvent(data, onSuccess);
    }
});