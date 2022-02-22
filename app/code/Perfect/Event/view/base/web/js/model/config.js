define([

], function () {
    var source = window,
        section = 'perfectTimetableConfig';

    return {
        getSaveEventUrl: function () {
            return this._getConfig('save_event_url');
        },
        getDeleteEventUrl: function () {
            return this._getConfig('delete_event_url');
        },
        getSearchClientUrl: function () {
            return this._getConfig('client_search_url');
        },
        getMasterServicesUrl: function () {
            return this._getConfig('master_services_url');
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