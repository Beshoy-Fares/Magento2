<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleAnalytics\Helper;

use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;

/**
 * GoogleAnalytics data helper
 *
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE = 'google/analytics/active';

    const XML_PATH_ACCOUNT_TYPE = 'google/analytics/account_type';

    const XML_PATH_ACCOUNT = 'google/analytics/account';

    const XML_PATH_ANONYMIZE = 'google/analytics/anonymize';

    /**
    * Account Types
    */
    const ACCOUNT_TYPE_GOOGLE_ANALYTICS = 0;

    /**
     * Whether GA is ready to use
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = $this->scopeConfig->getValue(self::XML_PATH_ACCOUNT, ScopeInterface::SCOPE_STORE, $store);
        return $accountId && $this->scopeConfig->isSetFlag(self::XML_PATH_ACTIVE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Whether anonymized IPs are active
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     * @since 100.2.0
     */
    public function isAnonymizedIpActive($store = null)
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ANONYMIZE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
    * Get Google Analytics Account Type
    *
    * @return string
    */
    public function getAccountType()
    {
 	    return $this->scopeConfig->getValue(
    	    self::XML_PATH_ACCOUNT_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Checks if Account Type is Google Analytics Account
     *
     * @return bool
     */
    public function isGoogleAnalyticsAccount()
    {
        return $this->getAccountType() == self::ACCOUNT_TYPE_GOOGLE_ANALYTICS;
    }
}
