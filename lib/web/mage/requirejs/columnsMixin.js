/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(function () {
    'use strict';

    var mixin = {
        /**
         * Adds ability to disable visibility control for a specific column.
         *
         * @param {Column} elem
         */
        isDisabled: function (elem) {
            return elem.blockVisibility || this._super();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
