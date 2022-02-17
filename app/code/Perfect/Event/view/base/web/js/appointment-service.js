define([
    'jquery',
    'uiElement',
    'ko',
    'mage/template',
    'Perfect_Event/js/view/appointment-template/service/default',
    'mage/collapsible'
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
            var target = $(appointment.target).closest('.data-row');
            target.fadeOut(200, function() { target.remove(); });
            return this;
        },
        initEvents: function () {
            $('.action-delete').off('click').on('click', this.deleteService.bind(this));
            /*$(this.appointmentTableContainer).find(".fieldset-wrapper").collapsible({
                "header": ".fieldset-wrapper-title",
                "content": ".admin__collapsible-content",
                "openedState": "_show",
                "closedState": "_hide",
                "active": true
            });*/
        }
    });
});