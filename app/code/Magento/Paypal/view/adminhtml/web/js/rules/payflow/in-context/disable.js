/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    return function ($target, $owner, data) {
        $target.find('label[for="' + $target.find(data.enableInContext).attr('id') + '"]').removeClass('enabled');
        $target.find(data.enableInContext + ' option[value="0"]').prop('selected', true);
        $target.find(data.enableInContext).prop('disabled', true);
    };
});
