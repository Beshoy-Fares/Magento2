<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BraintreeTwo\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

/**
 * Class DataAssignObserver
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    const PAYMENT_METHOD_NONCE = 'payment_method_nonce';
    const DEVICE_DATA = 'device_data';

    protected $additionalInformationList = [
        self::PAYMENT_METHOD_NONCE,
        self::DEVICE_DATA
    ];

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);
        /**
         * @TODO should be refactored after interface changes in payment and quote
         */
        $paymentInfo = $method->getInfoInstance();

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if ($data->getDataByKey($additionalInformationKey) !== null) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $data->getDataByKey($additionalInformationKey)
                );
            }
        }
    }
}
