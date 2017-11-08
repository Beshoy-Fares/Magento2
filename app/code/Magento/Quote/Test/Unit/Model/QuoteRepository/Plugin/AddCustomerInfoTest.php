<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Test\Unit\Model\QuoteRepository\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\Plugin\AddCustomerInfo;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Provide test for AddCustomerInfoPlugin.
 */
class AddCustomerInfoTest extends TestCase
{
    /**
     * @var AddCustomerInfo
     */
    private $testSubject;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->customerRepository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getById'])
            ->getMockForAbstractClass();
        $objectManager = new ObjectManager($this);
        $this->testSubject = $objectManager->getObject(
            AddCustomerInfo::class,
            ['customerRepository' => $this->customerRepository]
        );
    }

    /**
     * Test all necessary customer info will be added to cart, if absent.
     *
     * @return void
     */
    public function testBeforeSave()
    {
        $customerId = 1;

        /** @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject $customer */
        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->customerRepository->expects(self::once())
            ->method('getById')
            ->with(self::identicalTo($customerId))
            ->willReturn($customer);

        /** @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $cartRepository */
        $cartRepository = $this->getMockBuilder(CartRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        /** @var CartInterface|\PHPUnit_Framework_MockObject_MockObject $cart */
        $cart = $this->getMockBuilder(CartInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerIsGuest', 'getCustomerId', 'getData', 'setCustomer', 'setCustomerIsGuest'])
            ->getMockForAbstractClass();
        $cart->expects(self::once())
            ->method('getCustomerIsGuest')
            ->willReturn(false);
        $cart->expects(self::exactly(2))
            ->method('getCustomerId')
            ->willReturn($customerId);
        $cart->expects(self::once())
            ->method('getData')
            ->with(self::identicalTo(OrderInterface::CUSTOMER_EMAIL))
            ->willReturn(null);
        $cart->expects(self::once())
            ->method('setCustomer')
            ->with(self::identicalTo($customer))
            ->willReturnSelf();
        $cart->expects(self::once())
            ->method('setCustomerIsGuest')
            ->with(self::identicalTo(false))
            ->willReturnSelf();

        $this->testSubject->beforeSave($cartRepository, $cart);
    }
}
