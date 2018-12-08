<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Gateway\Http\Client;

/**
 * Class TransactionSale
 */
class TransactionSale extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;
        // sending store id and other additional keys are restricted by Braintree API
        unset($data['store_id']);

<<<<<<< HEAD
        return $this->adapterFactory->create($storeId)
            ->sale($data);
=======
        return $this->adapterFactory->create($storeId)->sale($data);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    }
}
