/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer'
    ],
    function ($, quote, urlBuilder, storage, url, errorProcessor, customer) {
        'use strict';

        return function (paymentData, redirectOnSuccess, messageContainer) {
            var serviceUrl,
                payload;

            redirectOnSuccess = redirectOnSuccess !== false;

            /** Checkout for guest and registered customer. */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                    quoteId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    email: quote.guestEmail,
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };
            }
            $('#checkout').trigger("processStart");
            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(
                function () {
                    if (redirectOnSuccess) {
                        window.location.replace(url.build('checkout/onepage/success/'));
                    }
                }
            ).fail(
                function (response) {
                    $('#checkout').trigger("processStop");
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);
