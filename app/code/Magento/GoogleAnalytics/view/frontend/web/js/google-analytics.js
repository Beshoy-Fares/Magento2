/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* jscs:disable */
/* eslint-disable */
define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    /**
     * @param {Object} config
     */
    return function (config) {
        /**
        *Magento Developer Id - Used for Gtag Configuration
        */
        var DEVELOPER_ID = 'dYjhlMD';
        var allowServices = false,
            allowedCookies,
            allowedWebsites,
            accountId,
            anonymizedIp;

        if (config.isCookieRestrictionModeEnabled) {
            allowedCookies = $.mage.cookies.get(config.cookieName);

            if (allowedCookies !== null) {
                allowedWebsites = JSON.parse(allowedCookies);

                if (allowedWebsites[config.currentWebsite] === 1) {
                    allowServices = true;
                }
            }
        } else {
            allowServices = true;
        }
        
        if (allowServices) {
            /* Global site tag (gtag.js) - Google Analytics */
            accountId = config.pageTrackingData.accountId;
            anonymizedIp = config.pageTrackingData.isAnonymizedIpActive;
            if (window.gtag) {
                gtag('config', accountId, { 'anonymize_ip': anonymizedIp });
                // Purchase Event
                if (config.ordersTrackingData.hasOwnProperty('currency')) {
                    var purchaseObject = config.ordersTrackingData.orders[0];
                    purchaseObject['items'] = config.ordersTrackingData.products;
                    gtag('event', 'purchase', purchaseObject);
                }
            } else {
                (function(d,s,u){
                    var gtagScript = d.createElement(s);
                    gtagScript.type = 'text/javascript';
                    gtagScript.async = true;
                    gtagScript.src = u;
                    d.head.insertBefore(gtagScript, d.head.children[0]);
                    })(document, 'script', 'https://www.googletagmanager.com/gtag/js?id=' + accountId);
        
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('set', DEVELOPER_ID, true);
                gtag('config', accountId, { 'anonymize_ip': anonymizedIp });
                // Purchase Event
                if (config.ordersTrackingData.hasOwnProperty('currency')) {
                    var purchaseObject = config.ordersTrackingData.orders[0];
                    purchaseObject['items'] = config.ordersTrackingData.products;
                    gtag('event', 'purchase', purchaseObject);
                }
            }
        }
    }
});
