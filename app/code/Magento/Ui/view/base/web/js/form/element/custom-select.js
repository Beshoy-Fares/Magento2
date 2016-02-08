/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    './abstract',
    'uiLayout',
    './select'
], function (_, utils, registry, Abstract, layout, select) {
    'use strict';

    return select.extend({
        defaults: {
            optionLabel: ''
        },
        initialize: function () {
            this._super();
            this.observe('optionLabel');
            this.updateLabel();
            this.value.subscribe(this.updateLabel, this);
        },
        updateLabel: function () {
            var self = this;
            var option = _.find(this.options(), function(option) {
                return option.value == self.value()
            });
            this.optionLabel(option ? option.label : this.caption);
        }
    });
});
