<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Multishipping\Test\TestStep;

use Magento\Customer\Test\Fixture\Customer;
use Magento\Multishipping\Test\Page\MultishippingCheckoutShipping;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Fill customer shipping information and proceed to next step.
 */
class FillShippingInformationStep implements TestStepInterface
{
    /**
     * Multishipping checkout shipping information page.
     *
     * @var MultishippingCheckoutShipping
     */
    protected $shippingInformation;

    /**
     * Customer fixture containing addresses.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Shipping method for this order.
     *
     * @var array
     */
    protected $shippingMethod;

    /**
     * @param MultishippingCheckoutShipping $shippingInformation
     * @param Customer $customer
     * @param array $shippingMethod
     */
    public function __construct(
        MultishippingCheckoutShipping $shippingInformation,
        Customer $customer,
        array $shippingMethod
    ) {
        $this->shippingInformation = $shippingInformation;
        $this->shippingMethod = $shippingMethod;
        $this->customer = $customer;
    }

    /**
     * Fill shipping information for each address and proceed to next step.
     *
     * @return void
     */
    public function run()
    {
        $shippingMethods = [];
<<<<<<< HEAD
        $addressCount = count($this->customer->getAddress());
        for ($i = 0; $i < $addressCount; $i++) {
=======
        for ($i = 0, $count = count($this->customer->getAddress()); $i < $count; $i++) {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            $shippingMethods[] = $this->shippingMethod;
        }
        $this->shippingInformation->getShippingBlock()->selectShippingMethod($shippingMethods);
    }
}
