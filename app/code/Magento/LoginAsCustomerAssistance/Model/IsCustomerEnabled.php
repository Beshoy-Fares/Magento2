<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\LoginAsCustomerAssistance\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\LoginAsCustomerAssistance\Model\ResourceModel\GetIsCustomerEnabled;

/**
 * Check if customer is Enabled
 */
class IsCustomerEnabled
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var GetIsCustomerEnabled
     */
    private $getIsCustomerEnabled;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        GetIsCustomerEnabled $getIsCustomerEnabled
    ) {
        $this->getIsCustomerEnabled = $getIsCustomerEnabled;
    }

    /**
     * Check if Customer is enabled by Customer id.
     *
     * @param int $customerId
     * @return bool
     */
    public function execute(int $customerId): bool
    {
        if (!isset($this->registry[$customerId])) {

            $this->registry[$customerId] = $this->getIsCustomerEnabled->execute($customerId);
        }

        return $this->registry[$customerId];
    }
}
