<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Gateway\Http\Client;

use Magento\Braintree\Gateway\Request\CaptureDataBuilder;
use Magento\Braintree\Gateway\Request\PaymentDataBuilder;

/**
 * Class TransactionSubmitForSettlement
 */
class TransactionSubmitForSettlement extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;
<<<<<<< HEAD
        // sending store id and other additional keys are restricted by Braintree API
        unset($data['store_id']);
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        return  $this->adapterFactory->create($storeId)
            ->submitForSettlement($data[CaptureDataBuilder::TRANSACTION_ID], $data[PaymentDataBuilder::AMOUNT]);
    }
}
