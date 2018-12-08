<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

/**
<<<<<<< HEAD
 * Class AccountConfirmation.
 * Checks if email confirmation required for customer.
=======
 * Class AccountConfirmation. Checks if email confirmation required for customer.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class AccountConfirmation
{
    /**
     * Configuration path for email confirmation.
     */
    const XML_PATH_IS_CONFIRM = 'customer/create_account/confirm';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Registry
     */
    private $registry;

    /**
<<<<<<< HEAD
=======
     * AccountConfirmation constructor.
     *
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
    }

    /**
     * Check if accounts confirmation is required.
     *
     * @param int|null $websiteId
     * @param int|null $customerId
     * @param string $customerEmail
     * @return bool
     */
    public function isConfirmationRequired($websiteId, $customerId, $customerEmail): bool
    {
        if ($this->canSkipConfirmation($customerId, $customerEmail)) {
            return false;
        }

<<<<<<< HEAD
        return (bool)$this->scopeConfig->getValue(
=======
        return $this->scopeConfig->isSetFlag(
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            self::XML_PATH_IS_CONFIRM,
            ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
    }

    /**
     * Check whether confirmation may be skipped when registering using certain email address.
     *
     * @param int|null $customerId
     * @param string $customerEmail
     * @return bool
     */
    private function canSkipConfirmation($customerId, $customerEmail): bool
    {
        if (!$customerId) {
            return false;
        }

        /* If an email was used to start the registration process and it is the same email as the one
           used to register, then this can skip confirmation.
           */
        $skipConfirmationIfEmail = $this->registry->registry("skip_confirmation_if_email");
        if (!$skipConfirmationIfEmail) {
            return false;
        }

        return strtolower($skipConfirmationIfEmail) === strtolower($customerEmail);
    }
}
