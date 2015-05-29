/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/flatrate',
        '../../model/shipping-rates-validation-rules/flatrate'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        upsShippingRatesValidator,
        upsShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator(upsShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('flatrate', upsShippingRatesValidationRules);
        return Component;
    }
);
