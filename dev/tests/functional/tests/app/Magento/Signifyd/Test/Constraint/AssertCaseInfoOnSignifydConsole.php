<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Signifyd\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Signifyd\Test\Fixture\SignifydAddress;
use Magento\Signifyd\Test\Fixture\SignifydData;
use Magento\Signifyd\Test\Page\SignifydConsole\SignifydCases;

/**
 * Assert that order information is correct on Signifyd case info page.
 */
class AssertCaseInfoOnSignifydConsole extends AbstractConstraint
{
    /**
     * Signifyd cases page.
     *
     * @var SignifydCases
     */
    private $signifydCases;

    /**
     * @param SignifydCases $signifydCases
     * @param SignifydAddress $billingAddress
     * @param SignifydData $signifydData
     * @param array $prices
     * @param string $orderId
     * @param string $customerFullName
     * @return void
     */
    public function processAssert(
        SignifydCases $signifydCases,
        SignifydAddress $billingAddress,
        SignifydData $signifydData,
        array $prices,
        $orderId,
        $customerFullName
    ) {
        $this->signifydCases = $signifydCases;

        $this->checkDeviceData();
        $this->checkShippingPrice($signifydData->getShippingPrice());
        $this->checkGuaranteeDisposition($signifydData->getGuaranteeDisposition());
        $cvvResponse = $signifydData->getCvvResponse();
        if (isset($cvvResponse)) {
            $this->checkCvvResponse($cvvResponse);
        }
        $this->checkAvsResponse($signifydData->getAvsResponse());
        $this->checkOrderId($orderId);
        $this->checkOrderAmount($prices['grandTotal']);
        $this->checkOrderAmountCurrency($prices['grandTotalCurrency']);
        $this->checkCardHolder($customerFullName);
        $this->checkBillingAddress($billingAddress);
    }

    /**
     * Checks device data are present.
     *
     * @return void
     */
    private function checkDeviceData()
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertTrue(
=======
        \PHPUnit_Framework_Assert::assertTrue(
>>>>>>> upstream/2.2-develop
            $this->signifydCases->getCaseInfoBlock()->isAvailableDeviceData(),
            'Device data are not available on case page in Signifyd console.'
        );
    }

    /**
     * Checks shipping price is correct.
     *
     * @param string $shippingPrice
     * @return void
     */
    private function checkShippingPrice($shippingPrice)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertContains(
=======
        \PHPUnit_Framework_Assert::assertContains(
>>>>>>> upstream/2.2-develop
            $shippingPrice,
            $this->signifydCases->getCaseInfoBlock()->getShippingPrice(),
            'Shipping price is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks guarantee disposition is correct.
     *
     * @param string $guaranteeDisposition
     * @return void
     */
    private function checkGuaranteeDisposition($guaranteeDisposition)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $guaranteeDisposition,
            $this->signifydCases->getCaseInfoBlock()->getGuaranteeDisposition(),
            'Guarantee disposition is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks CVV response is correct.
     *
     * @param string $cvvResponse
     * @return void
     */
    private function checkCvvResponse($cvvResponse)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $cvvResponse,
            $this->signifydCases->getCaseInfoBlock()->getCvvResponse(),
            'CVV response is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks AVS response is correct.
     *
     * @param string $avsResponse
     * @return void
     */
    private function checkAvsResponse($avsResponse)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $avsResponse,
            $this->signifydCases->getCaseInfoBlock()->getAvsResponse(),
            'AVS response is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks order id is correct.
     *
     * @param string $orderId
     * @return void
     */
    private function checkOrderId($orderId)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $orderId,
            $this->signifydCases->getCaseInfoBlock()->getOrderId(),
            'Order id is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks order amount is correct.
     *
     * @param string $amount
     * @return void
     */
    private function checkOrderAmount($amount)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            number_format($amount, 2),
            $this->signifydCases->getCaseInfoBlock()->getOrderAmount(),
            'Order amount is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks order amount currency is correct.
     *
     * @param string $currency
     * @return void
     */
    private function checkOrderAmountCurrency($currency)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $currency,
            $this->signifydCases->getCaseInfoBlock()->getOrderAmountCurrency(),
            'Order amount currency is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks card holder is correct.
     *
     * @param string $customerFullName
     * @return void
     */
    private function checkCardHolder($customerFullName)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertEquals(
=======
        \PHPUnit_Framework_Assert::assertEquals(
>>>>>>> upstream/2.2-develop
            $customerFullName,
            $this->signifydCases->getCaseInfoBlock()->getCardHolder(),
            'Card holder name is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * Checks billing address is correct.
     *
     * @param SignifydAddress $billingAddress
     * @return void
     */
    private function checkBillingAddress(SignifydAddress $billingAddress)
    {
<<<<<<< HEAD
        \PHPUnit\Framework\Assert::assertContains(
=======
        \PHPUnit_Framework_Assert::assertContains(
>>>>>>> upstream/2.2-develop
            $billingAddress->getStreet(),
            $this->signifydCases->getCaseInfoBlock()->getBillingAddress(),
            'Billing address is incorrect on case page in Signifyd console.'
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Case information is correct on case page in Signifyd console.';
    }
}
