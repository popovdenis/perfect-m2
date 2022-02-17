define([
    'jquery',
    'underscore',
    'ko',
    'Perfect_Event/js/form/element/abstract',
    'Perfect_Event/js/model/clients',
    'mage/translate',
    'Magento_Ui/js/lib/key-codes'
], function ($, _, ko, Abstract, ClientCollection, $t, keyCodes) {
    'use strict';

    return Abstract.extend({
        defaults: {
            _sequence: null,
            elementTmpl: 'Perfect_Event/form/client/input',
            clients: ko.observable(),
            clientsEmpty: ko.observable(),
            clientsDefault: ko.observable(),
            listVisible: ko.observable(false),
            showFilteredQuantity: true,
            itemsQuantity: '',
            inputValue: '',
            filterPlaceholder: '',
            filterRateLimit: 500,
            lastSelectable: false,
            showCheckbox: true,
            multiselectFocus: false,
            hoverClass: '_hover',
            separator: 'optgroup',
            quantityPlaceholder: $t('options'),
            closeBtn: true,
            closeBtnLabel: $t('Done'),
            filterRateLimitMethod: 'notifyAtFixedRate',
            listens: {
                listVisible: 'cleanHoveredElement',
                inputValue: 'searchClients',
                options: 'checkOptionsList'
            },
            selectedPlaceholders: {
                defaultPlaceholder: $t('Select...'),
                lotPlaceholders: $t('Selected')
            },
            loading: false,
            searchConfig: {
                dataType: 'json',
                type: 'POST',
                processData: false,
                contentType: false,
                showLoader: true,
                formOptions: {
                    form_key: window.FORM_KEY,
                    search: ''
                }
            },
            clientFields: {
                clientIdFieldName: '',
                clientPhoneFieldName: ''
            }
        },

        initialize: function () {
            this._super();

            var client = new ClientCollection({items: this.clientsDefault});
            this.clients = ko.observable(client.getList());

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe([
                'listVisible',
                'itemsQuantity',
                'inputValue',
                'loading',
                'multiselectFocus'
            ]);

            this.inputValue.extend({
                rateLimit: {
                    timeout: this.filterRateLimit,
                    method: this.filterRateLimitMethod
                }
            });

            return this;
        },

        /**
         * Toggle list visibility
         *
         * @returns {Object} Chainable
         */
        toggleListVisible: function () {
            this.listVisible(!this.listVisible());

            return this;
        },

        /**
         * Handler keydown event to filter options input
         *
         * @returns {Boolean} Returned true for emersion events
         */
        filterOptionsKeydown: function (data, event) {
            event.stopPropagation();

            var key = keyCodes[event.keyCode];
            if (key === 'pageDownKey' || key === 'pageUpKey') {
                event.preventDefault();

                return true;
            }

            return true;
        },

        /**
         * Search clients
         */
        searchClients: function () {
            var value = this.inputValue().trim().toLowerCase(),
                array = [],
                self = this;

            if (this.searchOptions) {
                return this.loadOptions(value);
            }

            this.cleanHoveredElement();

            if (!value) {
                this._setDefaultResults();
                // this._setItemsQuantity(false);

                return false;
            }

            if (this.inputValue()) {
                // search clients
                self.searchConfig.formOptions.search = this.inputValue();
                self._sequence = self._getXHRPromise(true);

                var newData = {};
                newData.paramName = this.inputName;
                newData.submit = function () {
                    newData.jqXHR = this.jqXHR = self._onSend(this);

                    return this.jqXHR;
                };

                newData.submit();

                return false;
            }
        },

        /**
         * Check selected option
         *
         * @param {String} value - option value
         * @return {Boolean}
         */
        isSelected: function (value) {
            return this.value() === value;
        },

        /**
         * Toggle activity list element
         *
         * @param {Object} data - selected option data
         * @param {Object} event
         * @returns {Object} Chainable
         */
        toggleOptionSelected: function (data, event) {
            if (!this.isSelected(data.client)) {
                this.value(data.client);
                var clientIdField = $('[name="' + this.clientFields.clientIdFieldName + '"]');
                var clientPhoneField = $('[name="' + this.clientFields.clientPhoneFieldName + '"]');

                if (clientIdField.length) {
                    clientIdField.val(data.client_id).trigger("change");
                }
                if (clientPhoneField.length) {
                    clientPhoneField.val(data.client_phone).trigger("change");
                }
            }

            this._hoverTo($(event.currentTarget));

            return this;
        },

        /**
         * Sets hover class to provided option element.
         *
         * @param {Element} element
         */
        _hoverTo: function (element) {
            if (this.hoveredElement) {
                $(this.hoveredElement).removeClass(this.hoverClass);
            }

            $(element).addClass(this.hoverClass);

            this.hoveredElement = element;
        },

        /**
         * Clean hoveredElement variable
         *
         * @returns {Object} Chainable
         */
        cleanHoveredElement: function () {
            if (this.hoveredElement) {
                $(this.hoveredElement).removeClass(this.hoverClass);
                this.hoveredElement = null;
            }

            return this;
        },

        /**
         * Set filtered items quantity
         *
         * @param {Object} data - option data
         */
        _setItemsQuantity: function (data) {
            if (this.showFilteredQuantity) {
                data
                    ? this.itemsQuantity(this.getItemsPlaceholder(data))
                    : this.itemsQuantity('');
            }
        },

        /**
         * Return formatted items placeholder.
         *
         * @param {Object} data - option data
         * @returns {String}
         */
        getItemsPlaceholder: function (data) {
            return data + ' ' + this.quantityPlaceholder;
        },

        /**
         * Handler outerClick event. Closed options list
         */
        outerClick: function () {
            this.listVisible() ? this.listVisible(false) : false;
        },

        _onSend: function (data) {
            var that = this,
                jqXHR,
                pipe,
                options = that._getAJAXSettings(data),
                send = function (resolve, args) {
                    jqXHR = jqXHR || (
                        resolve !== false && $.ajax(options)
                    ).done(function (result, textStatus, jqXHR) {
                        that._onDone(result, textStatus, jqXHR, options);
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        that._onFail(jqXHR, textStatus, errorThrown, options);
                    });
                    return jqXHR;
                };

            pipe = (this._sequence = this._sequence.pipe(send, send));

            return this._enhancePromise(pipe);
        },

        _onDone: function (items) {
            if (Object.entries(items).length) {
                this.clientsEmpty('');
                this.clients(items);
                this._setItemsQuantity(Object.entries(items).length);
            } else {
                this.clients([]);
                this._setItemsQuantity(0);
                this.clientsEmpty($t('Nothing found, please try to change your search keyword'));
            }
        },

        _setDefaultResults: function () {
            this.clientsEmpty('');
            this.clients(this.clientsDefault);
            this._setItemsQuantity(this.client().length);
        },

        _onFail: function (jqXHR, textStatus, errorThrown, options) {
            options.jqXHR = jqXHR;
            options.textStatus = textStatus;
            options.errorThrown = errorThrown;
            options.element.trigger('fail', null, options);
        },

        _getAJAXSettings: function (data) {
            var options = $.extend({}, this.searchConfig, data);
            options.url = this.searchConfig.url;

            let formData = new FormData();
            $.each(this.searchConfig.formOptions, function (name, value) {
                formData.append(name, value);
            });
            options.data = formData;

            return options;
        },

        // Creates and returns a Promise object enhanced with
        // the jqXHR methods abort, success, error and complete:
        _getXHRPromise: function (resolveOrReject, context, args) {
            var dfd = $.Deferred(),
                promise = dfd.promise();

            context = context || promise;
            if (resolveOrReject === true) {
                dfd.resolveWith(context, args);
            } else if (resolveOrReject === false) {
                dfd.rejectWith(context, args);
            }
            promise.abort = dfd.promise;

            return this._enhancePromise(promise);
        },

        // Maps jqXHR callbacks to the equivalent
        // methods of the given Promise object:
        _enhancePromise: function (promise) {
            promise.success = promise.done;
            promise.error = promise.fail;
            promise.complete = promise.always;

            return promise;
        },

        /**
         * Set true to observable variable multiselectFocus
         * @param {Object} ctx
         * @param {Object} event - focus event
         */
        onFocusIn: function (ctx, event) {
            this.multiselectFocus(true);
        },

        /**
         * Set false to observable variable multiselectFocus
         * and close list
         */
        onFocusOut: function () {
            this.multiselectFocus(false);
        },
    });
});