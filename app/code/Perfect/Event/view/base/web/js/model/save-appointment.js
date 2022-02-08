define([
    'jquery',
    'timetableConfig'
], function ($, config) {
    'use strict';

    var _saveAppointment = function (appointment, onSuccess) {
        $.ajax({
            url: config.getSaveAppointmentUrl(),
            data: {form_key: window.FORM_KEY, appointment: appointment.originalData},
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
        _saveAppointment(data, onSuccess);
    }
});