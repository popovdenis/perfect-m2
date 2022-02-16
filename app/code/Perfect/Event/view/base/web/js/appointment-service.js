define([
    'jquery',
    'uiElement',
    'ko',
    'mage/template',
    'Perfect_Event/js/view/appointment-template/service/default'
], function($, Component, ko, mageTemplate, defaultTemplate) {
    'use strict';

    return Component.extend({
        defaults: {
            appointmentTableContainer: '.appointment-service-table tbody'
        },
        initialize: function () {
            this._super();
        },
        addService: function () {
            var templateText = $(mageTemplate(defaultTemplate())());

            $(this.appointmentTableContainer).append(templateText);

            this.initEvents();

            return this;
        },
        deleteService: function (appointment) {
            jQuery(appointment.target).closest('.data-row').remove();
            return this;
        },
        initEvents: function () {
            $('.action-delete').off('click').on('click', this.deleteService.bind(this));
        }
    });
});