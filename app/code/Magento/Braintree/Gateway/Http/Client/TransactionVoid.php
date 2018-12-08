<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Gateway\Http\Client;

class TransactionVoid extends AbstractTransaction
{
    /**
     * Process http request
     * @param array $data
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;
<<<<<<< HEAD
        // sending store id and other additional keys are restricted by Braintree API
        unset($data['store_id']);

        return $this->adapterFactory->create($storeId)
            ->void($data['transaction_id']);
=======

        return $this->adapterFactory->create($storeId)->void($data['transaction_id']);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    }
}
