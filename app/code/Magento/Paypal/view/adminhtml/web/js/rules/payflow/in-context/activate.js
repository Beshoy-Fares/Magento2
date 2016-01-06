/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/modal/alert'
], function (alert) {
    'use strict';

    return function ($target, $owner, data) {
        $target.find('label[for="' + $target.find(data.enableInContext).attr('id') + '"]').addClass('enabled');

        alert({
            content: 'Please enable PayPal Express Checkout for using this option.'
        });
    };
});
