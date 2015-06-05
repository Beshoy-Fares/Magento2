/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    ['ko'],
    function(ko) {
        var billingAddress = ko.observable(null);
        var shippingAddress = ko.observable(null);
        var shippingMethod = ko.observable(null);
        var paymentMethod = ko.observable(null);
        var quoteData = window.checkoutConfig.quoteData;
        var basePriceFormat = window.checkoutConfig.basePriceFormat;
        var priceFormat = window.checkoutConfig.priceFormat;
        var selectedShippingMethod = ko.observable(window.checkoutConfig.selectedShippingMethod);
        var storeCode = window.checkoutConfig.storeCode;
        var totals = ko.observable(window.checkoutConfig.totalsData);
        var checkoutMethod = ko.observable(null);
        var shippingCustomOptions = ko.observable(null);
        var formattedShippingAddress = ko.observable(null);
        var formattedBillingAddress = ko.observable(null);
        var collectedTotals = ko.observable({});
        var isCustomerLoggedIn = ko.observable(window.checkoutConfig.isCustomerLoggedIn);
        return {
            shippingAddress: shippingAddress,

            getQuoteId: function() {
                return quoteData.entity_id;
            },
            isVirtual: function() {
                return !!Number(quoteData.is_virtual);
            },
            getPriceFormat: function() {
                return priceFormat;
            },
            getBasePriceFormat: function() {
                return basePriceFormat;
            },
            getItems: function() {
                return window.checkoutConfig.quoteItemData;
            },
            getTotals: function() {
                return totals
            },
            getIsCustomerLoggedIn: function() {
                return isCustomerLoggedIn;
            },
            setIsCustomerLoggedIn: function(status) {
                isCustomerLoggedIn(status);
            },
            getTotalByCode: function(code) {
                if (!totals()) {
                    return null;
                }
                for(var i in totals().calculated_totals) {
                    var total = totals().calculated_totals[i];
                    if (total.code == code) {
                        return total;
                    }
                }
                return null;
            },
            setTotals: function(totalsData) {
                if (_.isObject(totalsData.extension_attributes)) {
                    _.each(totalsData.extension_attributes, function(element, index) {
                        totalsData[index] = element;
                    });
                }
                totals(totalsData);
                this.setCollectedTotals('subtotal_with_discount', parseFloat(totalsData.subtotal_with_discount));
            },
            setBillingAddress: function (address) {
                billingAddress(address);
            },
            getBillingAddress: function() {
                return billingAddress;
            },
            setFormattedBillingAddress: function (address) {
                formattedBillingAddress(address);
            },
            getFormattedBillingAddress: function() {
                return formattedBillingAddress;
            },
            setFormattedShippingAddress: function (address) {
                formattedShippingAddress(address);
            },
            getFormattedShippingAddress: function() {
                return formattedShippingAddress;
            },
            setPaymentMethod: function(paymentMethodCode) {
                paymentMethod(paymentMethodCode);
            },
            getPaymentMethod: function() {
                return paymentMethod;
            },
            setShippingMethod: function(shippingMethodCode) {
                shippingMethod(shippingMethodCode);
            },
            getShippingMethod: function() {
                return shippingMethod;
            },
            getSelectedShippingMethod: function() {
                return selectedShippingMethod;
            },
            setSelectedShippingMethod: function(shippingMethod) {
                selectedShippingMethod(shippingMethod);
            },
            getStoreCode: function() {
                return storeCode;
            },
            getCheckoutMethod: function() {
                return checkoutMethod;
            },
            setCheckoutMethod: function(method) {
                checkoutMethod(method);
            },
            setShippingCustomOptions: function(customOptions) {
                shippingCustomOptions(customOptions);
            },
            getShippingCustomOptions: function() {
                return shippingCustomOptions;
            },
            setCollectedTotals: function(code, value) {
                var totals = collectedTotals();
                totals[code] = value;
                collectedTotals(totals);
            },
            getCalculatedTotal: function() {
                var total = 0.;
                _.each(collectedTotals(), function(value) {
                    total += value;
                });
                return total;
            }
        };
    }
);
