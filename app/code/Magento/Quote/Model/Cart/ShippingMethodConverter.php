<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Model\Cart;

/**
 * Quote shipping method data.
 *
 */
class ShippingMethodConverter
{
    /**
     * Shipping method builder.
     *
     * @var \Magento\Quote\Api\Data\ShippingMethodDataBuilder
     */
    protected $builder;

    /**
     * Constructs a shipping method builder object.
     *
     * @param \Magento\Quote\Api\Data\ShippingMethodDataBuilder $builder Shipping method builder.
     * @param \Magento\Framework\Store\StoreManagerInterface $storeManager Store manager interface.
     */
    public function __construct(
        \Magento\Quote\Api\Data\ShippingMethodDataBuilder $builder,
        \Magento\Framework\Store\StoreManagerInterface $storeManager
    ) {
        $this->builder = $builder;
        $this->storeManager = $storeManager;
    }

    /**
     * Converts a specified rate model to a shipping method data object.
     *
     * @param string $quoteCurrencyCode The quote currency code.
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @return mixed Shipping method data object.
     */
    public function modelToDataObject($rateModel, $quoteCurrencyCode)
    {
        /** @var \Magento\Directory\Model\Currency $currency */
        $currency = $this->storeManager->getStore()->getBaseCurrency();

        $errorMessage = $rateModel->getErrorMessage();
        $data = [
            ShippingMethod::CARRIER_CODE => $rateModel->getCarrier(),
            ShippingMethod::METHOD_CODE => $rateModel->getMethod(),
            ShippingMethod::CARRIER_TITLE => $rateModel->getCarrierTitle(),
            ShippingMethod::METHOD_TITLE => $rateModel->getMethodTitle(),
            ShippingMethod::SHIPPING_AMOUNT => $currency->convert($rateModel->getPrice(), $quoteCurrencyCode),
            ShippingMethod::BASE_SHIPPING_AMOUNT => $rateModel->getPrice(),
            ShippingMethod::AVAILABLE => empty($errorMessage),
        ];
        $this->builder->populateWithArray($data);
        return $this->builder->create();
    }
}
