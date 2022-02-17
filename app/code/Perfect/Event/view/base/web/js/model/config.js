define([

], function () {
    var source = window,
        section = 'perfectTimetableConfig';

    return {
        getSaveAppointmentUrl: function () {
            return this._getConfig('save_appointment_url');
        },
        getDeleteAppointmentUrl: function () {
            return this._getConfig('delete_appointment_url');
        },
        getSearchClientUrl: function () {
            return this._getConfig('client_search_url');
        },
        /**
         * Retrieve config value by given key.
         * Config will be fetched from defined section.
         *
         * @param {String} key
         * @returns {null|*}
         */
        _getConfig: function (key) {
            if (typeof source[section] === 'object'
                && typeof source[section][key] !== 'undefined'
            ) {
                return source[section][key];
            }

            return null;
        }
    };
});