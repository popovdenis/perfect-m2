define([
    'Perfect_Event/js/jqwidgets/jqxcore',
    'Perfect_Event/js/jqwidgets/jqxbuttons',
    'Perfect_Event/js/jqwidgets/jqxscrollbar',
    'Perfect_Event/js/jqwidgets/jqxdata',
    'Perfect_Event/js/jqwidgets/jqxdate',
    'Perfect_Event/js/jqwidgets/jqxscheduler',
    'Perfect_Event/js/jqwidgets/jqxscheduler.api',
    'Perfect_Event/js/jqwidgets/jqxdatetimeinput',
    'Perfect_Event/js/jqwidgets/jqxmenu',
    'Perfect_Event/js/jqwidgets/jqxcalendar',
    'Perfect_Event/js/jqwidgets/jqxtooltip',
    'Perfect_Event/js/jqwidgets/jqxwindow',
    'Perfect_Event/js/jqwidgets/jqxcheckbox',
    'Perfect_Event/js/jqwidgets/jqxlistbox',
    'Perfect_Event/js/jqwidgets/jqxdropdownlist',
    'Perfect_Event/js/jqwidgets/jqxnumberinput',
    'Perfect_Event/js/jqwidgets/jqxradiobutton',
    'Perfect_Event/js/jqwidgets/jqxinput',
    'Perfect_Event/js/jqwidgets/globalization/globalize',
    'Perfect_Event/js/jqwidgets/globalization/globalize.culture.ru-RU',
    'Perfect_Event/js/jqwidgets/demos',
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