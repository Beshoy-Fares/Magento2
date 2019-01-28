/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    return function (shippingAddress) {
    	var address = null;
    	address = quote.billingAddress();
        quote.shippingAddress(shippingAddress);
        quote.billingAddress(address);

    };
});
