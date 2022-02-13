define([
    'uiClass',
    'Perfect_Event/js/lib/md5/core',
    'Perfect_Event/js/lib/md5/md5'
], function (Class, CryptoJS) {
    'use strict';

    return Class.extend({
        hash: function (string) {
            return CryptoJS.MD5(string).toString();
        }
    });
});