/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    ['jquery'],
    function ($) {
        'use strict';

        return function (config, element) {
            $(element).click(config, function () {
                confirmSetLocation(config.message, config.url);
            });
        };
    }
);
