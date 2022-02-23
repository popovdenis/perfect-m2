define(['mage/translate'], function ($t) {
    return function () {
        return '' +
            '<tr class="data-row">\n' +
                '<td class="_no-header">\n' +
                    '<div class="fieldset-wrapper admin__collapsible-block-wrapper">\n' +
                        '<div class="admin__collapsible-content _show" style="margin-bottom: 0;">\n' +
                            '<div class="fieldset-wrapper admin__collapsible-block-wrapper _show _no-header">\n' +
                                '<div class="admin__fieldset-wrapper-content">\n' +
                                    '<fieldset class="admin__fieldset">\n' +
                                        '<fieldset class="admin__field">\n' +
                                            '<div class="admin__field-control admin__field-group-columns admin__control-group-equal admin__control-grouped">\n' +
                                                '<div class="admin__field" style="width: 75%">\n' +
                                                    '<div class="admin__field-label">\n' +
                                                        '<label for="service_name"><span>' + $t('Service Name') + '</span></label>\n' +
                                                    '</div>\n' +
                                                    '<div class="admin__field-control">\n' +
                                                        // '<input class="admin__control-text service_name" type="text" name="services[<%- data.row_index %>][service_name]" style="float: left;width: 90%;" />\n' +
                                                        '<div class="admin__action-multiselect-wrap action-select-wrap" style="width: 87%;float: left;">\n' +
                                                            '<div class="action-select admin__action-multiselect">\n' +
                                                                '<div class="admin__action-multiselect-text selected-service-value"></div>\n' +
                                                                '<div class="action-menu" data-bind="css:{_active: listVisible}" style="z-index: 1;">\n' +
                                                                    '<div class="services-detail-empty" data-bind="text: providersEmpty()"></div>\n' +
                                                                    '<ul class="services-search-items admin__action-multiselect-menu-inner _root">\n' +
                                                                    '</ul>\n' +
                                                                '</div>\n' +
                                                            '</div>\n' +
                                                        '</div>\n' +
                                                        '<div class="input-group-btn" style="float: left;margin-left: 25px;">\n' +
                                                            '<button class="btn btn-default btn-delete input-sm fsize15" type="button"><i class="fa fa-trash-o"></i></button>\n' +
                                                        '</div>\n' +
                                                    '</div>\n' +
                                                '</div>\n' +
                                                '<div class="admin__field" style="width: 25%">\n' +
                                                    '<div class="admin__field-label">\n' +
                                                        '<label><span>' + $t('Quantity') + '</span></label>\n' +
                                                    '</div>\n' +
                                                '<div class="admin__field-control">\n' +
                                                    '<div class="minus-plus-group">\n' +
                                                        '<span class="input-group-btn">\n' +
                                                            '<button type="button" class="btn btn-default input-sm btn-qty-minus">\n' +
                                                                '<i class="fa fa-minus"></i>\n' +
                                                            '</button>\n' +
                                                        '</span>\n' +
                                                        '<input type="text" class="form-control input-qty admin__control-text" min="0" max="1000" name="services[<%- data.row_index %>][quantity]" value="1" placeholder="' + $t('Quantity-short') + '">\n' +
                                                        '<span class="input-group-btn">\n' +
                                                            '<button type="button" class="btn btn-default input-sm btn-qty-plus">\n' +
                                                                '<i class="fa fa-plus"></i>\n' +
                                                            '</button>\n' +
                                                        '</span>\n' +
                                                    '</div>\n' +
                                                '</div>\n' +
                                            '</div>\n' +
                                        '</fieldset>\n' +
                                    '</fieldset>\n' +
                                '</div>\n' +
                            '</div>\n' +
                        '</div>\n' +
                    '</div>\n' +
                '</td>\n' +
            '</tr>'
    }
});