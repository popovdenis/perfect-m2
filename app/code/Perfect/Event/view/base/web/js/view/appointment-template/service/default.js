define(['mage/translate', 'Perfect_Event/js/appointment-service'], function ($t, service) {
    return function () {
        return '' +
            '<tr class="data-row">\n' +
                '<td class="_no-header">\n' +
                    '<div class="fieldset-wrapper admin__collapsible-block-wrapper">\n' +
                        '<div class="fieldset-wrapper-title">\n' +
                            '<div class="title admin__collapsible-title">\n' +
                                '<span>' + $t('New Service') + '</span>\n' +
                                '<button type="button" class="action-delete"><span>' + $t('Удалить') + '</span></button>\n' +
                            '</div>\n' +
                        '</div>\n' +
                        '<div class="admin__collapsible-content _show">\n' +
                            '<div class="fieldset-wrapper admin__collapsible-block-wrapper _show _no-header">\n' +
                                '<div class="admin__fieldset-wrapper-content">\n' +
                                    '<fieldset class="admin__fieldset">\n' +
                                        '<fieldset class="admin__field">\n' +
                                            '<div class="admin__field-control admin__field-group-columns admin__control-group-equal admin__control-grouped">\n' +
                                                '<div class="admin__field">\n' +
                                                    '<div class="admin__field-label">\n' +
                                                        '<label for="JELIY6D">\n' +
                                                            '<span>' + $t('Service Name') + '</span>\n' +
                                                        '</label>\n' +
                                                    '</div>\n' +
                                                    '<div class="admin__field-control">\n' +
                                                        '<input class="admin__control-text" type="text" name="product[options][0][title]"\n' +
                                                        'id="JELIY6D" maxlength="255">\n' +
                                                    '</div>\n' +
                                                '</div>\n' +
                                                '<div class="admin__field">\n' +
                                                    '<div class="admin__field-label">\n' +
                                                        '<label for="JELIY5M">\n' +
                                                            '<span>' + $t('Quantity') + '</span>\n' +
                                                        '</label>\n' +
                                                    '</div>\n' +
                                                '<div class="admin__field-control">\n' +
                                                    '<div class="minus-plus-group">\n' +
                                                        '<span class="input-group-btn">\n' +
                                                            '<button type="button" class="btn btn-default input-sm">\n' +
                                                                '<i class="fa fa-minus"></i>\n' +
                                                            '</button>\n' +
                                                        '</span>\n' +
                                                        '<input type="text" class="form-control input-number admin__control-text" min="0" max="1000" name="amount" value="1" placeholder="Кол-во">\n' +
                                                        '<span class="input-group-btn">\n' +
                                                            '<button type="button" class="btn btn-default input-sm">\n' +
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