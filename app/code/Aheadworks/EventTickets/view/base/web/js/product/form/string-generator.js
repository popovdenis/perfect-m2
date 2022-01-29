define([], function () {
    'use strict';

    return {

        /**
         * Generate sting with alphabetic characters
         *
         * @param {Number} length
         * @returns {String}
         */
        generateAlphabeticString: function (length) {
            var chars = 'abcdefghijklmnopqrstuvwxyz',
                result = '',
                rnum;

            for (var i = 0; i < length; i++) {
                rnum = Math.floor(Math.random() * chars.length);
                result += chars.substring(rnum, rnum + 1);
            }

            return result;
        }
    };
});
