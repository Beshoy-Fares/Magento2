/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    'Magento_Ui/js/form/element/boolean'
], function (_, utils, uiRegistry, Boolean) {
    'use strict';

    return Boolean.extend({
        defaults: {
        },

        /**
         * Defines if value has changed
         *
         * @returns {Boolean}
         */
        onUpdate: function () {
            this._super();
            var isDisabled = !this.value();
            var selector = '[id=coupons_information_fieldset] input, [id=coupons_information_fieldset] select, '
                + '[id=coupons_information_fieldset] button, [id=couponCodesGrid] input, [id=couponCodesGrid] select, '
                + '[id=couponCodesGrid] button';
            _.each(
                document.querySelectorAll(selector),
                function(e) {
                    e.disabled = isDisabled;
                }
            );
        }
    });
});
