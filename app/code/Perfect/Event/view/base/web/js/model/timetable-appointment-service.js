define([
    'saveAppointment',
    'deleteAppointment'
], function (saveAppointment, deleteAppointment) {
    return {
        sendAppointment: function (appointments) {
            return new Promise(function(resolve) {
                saveAppointment(appointments, function (appointmentData) {
                    resolve(appointmentData);
                });
            });
        },
        deleteAppointment: function (appointment) {
            return new Promise(function(resolve) {
                deleteAppointment(appointment, function (appointmentData) {
                    resolve(appointmentData);
                });
            });
        }
    };
});