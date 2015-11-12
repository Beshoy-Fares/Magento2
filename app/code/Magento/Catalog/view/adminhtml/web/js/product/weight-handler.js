/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return {

        $weightSwitcher: $('[data-role=weight-switcher]'),
        $weight: $('#weight'),

        /**
         * Is locked
         * @returns {*}
         */
        isLocked: function () {
            return this.$weight.is('[data-locked]');
        },

        /**
         * Disabled
         */
        disabled: function () {
            this.$weight.addClass('ignore-validate').prop('disabled', true);
        },

        /**
         * Enabled
         */
        enabled: function () {
            this.$weight.removeClass('ignore-validate').prop('disabled', false);
        },

        /**
         * Switch Weight
         * @returns {*}
         */
        switchWeight: function () {
            return this.productHasWeightBySwitcher() ? this.enabled() : this.disabled();
        },

        /**
         * Hide weight switcher
         */
        hideWeightSwitcher: function () {
            this.$weightSwitcher.hide();
        },

        /**
         * Has weight swither
         * @returns {*}
         */
        hasWeightSwither: function () {
            return this.$weightSwitcher.is(':visible');
        },

        /**
         * Product has weight
         * @returns {Bool}
         */
        productHasWeightBySwitcher: function () {
            return $('input:checked', this.$weightSwitcher).val() === '1';
        },

        /**
         * Change
         * @param {String} data
         */
        change: function (data) {
            var value = data !== undefined ? +data : !this.productHasWeightBySwitcher();

            $('input[value=' + value + ']', this.$weightSwitcher).prop('checked', true);
        },

        /**
         * Constructor component
         */
        'Magento_Catalog/js/product/weight-handler': function () {
            this.bindAll();

            if (this.hasWeightSwither()) {
                this.switchWeight();
            }
        },

        /**
         * Bind all
         */
        bindAll: function () {
            this.$weightSwitcher.find('input').on('change', this.switchWeight.bind(this));
        }
    };
});
