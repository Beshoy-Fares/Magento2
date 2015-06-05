/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function (Component, quote, priceUtils, totals, $t) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/discount'
            },
        totals: quote.getTotals(),
        getTitle: function() {
            var discountTotal = totals.getTotalByCode('discount');
            if (discountTotal) {
                var title = $t(discountTotal.title);
                return title.replace("%1", this.totals().coupon_code);
            }
            return null;
        },
        getPureValue: function() {
            var price = 0;
            if (this.totals() && this.totals().discount_amount) {
                price = parseFloat(this.totals().discount_amount);
            }
            return price;
        },
        getValue: function() {
            var price = 0;
            if (this.totals() && this.totals().discount_amount) {
                price = parseFloat(this.totals().discount_amount);
            }
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
        });
    }
);
