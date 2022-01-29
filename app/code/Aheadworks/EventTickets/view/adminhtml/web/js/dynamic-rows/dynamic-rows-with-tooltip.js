define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * Init header elements
         */
        initHeader: function () {
            var labels = [],
                data;

            if (!this.labels().length) {
                _.each(this.childTemplate.children, function (cell) {
                    data = this.createHeaderTemplate(cell.config);
                    cell.config.labelVisible = false;

                    _.extend(data, {
                        defaultLabelVisible: data.visible(),
                        label: cell.config.label,
                        name: cell.name,
                        required: !!cell.config.validation,
                        columnsHeaderClasses: cell.config.columnsHeaderClasses,
                        sortOrder: cell.config.sortOrder,
                        tooltip: cell.config.headerTooltip,
                        tooltipTpl: cell.config.headerTooltipTpl
                    });

                    labels.push(data);
                }, this);
                this.labels(_.sortBy(labels, 'sortOrder'));
            }
        },

        /**
         * @inheritDoc
         */
        setChangedForCurrentPage: function () {
            this._super();
            this._sendTriggerUpdate();
        },

        /**
         * @inheritDoc
         */
        setDefaultState: function (data) {
            this._super(data);
            this._sendTriggerUpdate();
        },

        /**
         * @inheritDoc
         */
        initElement: function(elem) {
            this._super(elem);
            this._sendTriggerUpdate();
        },

        /**
         * Send update trigger
         *
         * @private
         */
        _sendTriggerUpdate: function () {
            this.source.trigger('updateTrigger');
        }
    });
});
