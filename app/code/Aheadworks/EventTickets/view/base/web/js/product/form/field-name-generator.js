define([
    'mageUtils',
], function (utils) {
    'use strict';

    return {

        /**
         * Generate name for provided filed using scope
         *
         * @param {String} scope
         * @param {String|NULL} field
         * @returns {String}
         */
        generate: function (scope, field) {
            var scopePaths, name, baseInputName;

            scopePaths = scope.split('.');
            name = scopePaths.length > 1 ? scopePaths.slice(1) : scopePaths;
            baseInputName = utils.serializeName(name.join('.'));

            return field ? baseInputName + '[' + field + ']' : baseInputName;
        }
    };
});
