define([
    'jquery',
    'timetableConfig'
], function ($, config) {
    'use strict';

    var _deleteAppointment = function (appointment, onSuccess) {
        $.ajax({
            url: config.getDeleteAppointmentUrl(),
            data: {form_key: window.FORM_KEY, appointment: appointment},
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
        _deleteAppointment(data, onSuccess);
    }
});