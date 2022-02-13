define([
    'jquery',
    'uiClass',
    'Magento_Ui/js/modal/modal',
    'jquery/ui'
], function ($, Class, modal) {
    'use strict';

    return Class.extend({
        options: {
            appointmentModal: null
        },

        initialize: function (options) {
            this.options = options;

            this.initPopup();

            return this;
        },

        initPopup: function () {
            var self = this;

            modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: '',
                clickableOverlay: true,
                buttons: [{
                    text: $.mage.__('Save'),
                    class: 'action primary',
                    click: function (event) {
                        this.closeModal();
                        self._createAppointment(event);
                    }
                }]
            }, $(this.options.appointmentModal));

            $(this.options.appointmentModal).on('modalclosed', function() {
                self.activePopup = false;
            });

            let subscribeForm = $('.form.subscribe');

            $(subscribeForm).on('submit', function(e) {
                e.preventDefault();
                let email = $('#newsletter').val();

                if ($(subscribeForm).valid()) {
                    $.ajax({
                        url: 'newsletter/subscriber/new/',
                        type: 'POST',
                        data: {
                            'email' : email
                        },
                        dataType: 'json',
                        showLoader: true,
                        complete: function(data, status) {
                            let response = JSON.parse(data.responseText);
                            $('.newsletter-modal').text(response.message).modal('openModal');
                        }
                    });
                }
            });
        },

        populatePopup: function (appointment) {
            // client
            $('input[name="appointment[client-id]"]').val(appointment.id);

            // services
            $('input[name="appointment[service-name]"]').val(appointment.title);

            // employee
            $('input[name="appointment[employee-id]"]').val(appointment.extendedProps.master_id);
            $('input[name="appointment[employee-name]"]').val(appointment.extendedProps.master_name);
        },

        openPopup: function () {
            $(this.options.appointmentModal).modal("openModal");
        }
    });
});