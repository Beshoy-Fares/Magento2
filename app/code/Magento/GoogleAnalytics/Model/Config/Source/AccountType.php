<?php
/**
 * Google AdWords conversation value type source
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleAnalytics\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class AccountType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get account  type option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\GoogleAnalytics\Helper\Data::ACCOUNT_TYPE_UNIVERSAL_ANALYTICS,
                'label' => __('Universal Analytics'),
            ],
            [
                'value' => \Magento\GoogleAnalytics\Helper\Data::ACCOUNT_TYPE_GOOGLE_ANALYTICS,
                'label' => __('Google Analytics 4'),
            ]
        ];
    }
}
