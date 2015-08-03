define([
    'ko',
    'jquery',
    'underscore',
    'uiRegistry',
    'mage/utils/dom-observer',
    'Magento_Ui/js/lib/ko/extender/bound-nodes',
    'uiClass'
], function (ko, $, _, registry, domObserver, boundNodes, Class) {
    'use strict';

    return Class.extend({
        defaults: {
            rowSelector: 'tbody tr.data-row',
            headerButtonsTmpl:
                '<!-- ko template: headerButtonsTmpl --><!-- /ko -->',
            rowTmpl:
                '<!-- ko with: _editor -->' +
                    '<!-- ko scope: formRecordName($index(), true) -->' +
                        '<!-- ko template: rowTmpl --><!-- /ko -->' +
                    '<!-- /ko -->' +
                    '<!-- ko if: isActive($index(), true) && isSingleEditing() -->' +
                        '<!-- ko template: rowButtonsTmpl --><!-- /ko -->' +
                    '<!-- /ko -->' +
               '<!-- /ko -->'
        },

        /**
         * Initializes view component.
         *
         * @returns {View} Chainable.
         */
        initialize: function () {
            _.bindAll(this, 'onRowAdd', 'onRootAdd');

            this._super();

            this.model = registry.get(this.model);

            registry.get(this.columnsProvider, function (columns) {
                boundNodes.get(columns, this.onRootAdd);
            }.bind(this));

            return this;
        },

        /**
         * Initializes columns root container.
         *
         * @param {HTMLElement} node
         * @returns {View} Chainable.
         */
        initRoot: function (node) {
            var table       = node.querySelector(':scope > table'),
                buttonsHtml = $(this.headerButtonsTmpl);

            buttonsHtml.insertBefore(node);
            ko.applyBindings(this.model, buttonsHtml[0]);

            if (table) {
                this.initTable(table);
            }

            return this;
        },

        /**
         * Initializes table element.
         *
         * @param {HTMLTableElement} table
         * @returns {View} Chainable.
         */
        initTable: function (table) {
            var model = this.model,
                ctx = ko.contextFor(table);

            ko.applyBindingsToNode(table, {
                css: {
                    '_in-edit': ko.computed(function () {
                        return model.hasActive();
                    })
                }
            }, ctx);

            domObserver.get(this.rowSelector, this.onRowAdd, table);

            return this;
        },

        /**
         * Initializes table row.
         *
         * @param {HTMLTableRowElement} row
         * @returns {View} Chainable.
         */
        initRow: function (row) {
            var ctx     = ko.contextFor(row),
                model   = this.model,
                rowHtml = $(this.rowTmpl);

            ko.applyBindingsToNode(row, {
                visible: ko.computed(function () {
                    return !model.isActive(ctx.$index(), true);
                })
            }, ctx);

            ctx._editor = model;

            rowHtml.insertBefore(row);
            ko.applyBindings(ctx, rowHtml[0]);

            return this;
        },

        /**
         * Listener of the tables' rows appearance.
         *
         * @param {HTMLTableRowElement} row
         */
        onRowAdd: function (row) {
            this.initRow(row);
        },

        /**
         * Listener of the root node appearance.
         *
         * @param {HTMLElement} node
         */
        onRootAdd: function (node) {
            if ($(node).is('.admin__data-grid-wrap')) {
                this.initRoot(node);
            }
        }
    });
});
