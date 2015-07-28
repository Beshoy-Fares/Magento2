/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "uiComponent"
], function (Component) {
    "use strict";

    return Component.extend({
        initData: [],
        initialize: function () {
            this._super();
            this.steps = [];
        },
        initElement: function (step) {
            step.initData = this.initData;
            this.steps.push(step);
        }
    });
});
