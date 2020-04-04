<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Gateway\Data;

use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Gateway\Data\Order\OrderAdapter;
use Magento\Payment\Gateway\Data\Order\OrderAdapterFactory;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapter;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapterFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentDataObjectFactoryTest extends TestCase
{
    /**
     * @var PaymentDataObjectFactory
     */
    private $model;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * @var OrderAdapterFactory|MockObject
     */
    private $orderAdapterFactoryMock;

    /**
     * @var QuoteAdapterFactory|MockObject
     */
    private $quoteAdapterFactoryMock;

    /**
     * @var PaymentDataObject|MockObject
     */
    private $paymentDataObjectMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->getMockForAbstractClass();

        $this->orderAdapterFactoryMock =
            $this->getMockBuilder(OrderAdapterFactory::class)
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->quoteAdapterFactoryMock =
            $this->getMockBuilder(QuoteAdapterFactory::class)
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->paymentDataObjectMock =
            $this->createMock(PaymentDataObjectInterface::class);

        $this->model = new PaymentDataObjectFactory(
            $this->objectManagerMock,
            $this->orderAdapterFactoryMock,
            $this->quoteAdapterFactoryMock
        );
    }

    public function testCreatePaymentDataObjectFromOrder()
    {
        /** @var Order $orderMock */
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var OrderAdapter $orderAdapterMock */
        $orderAdapterMock = $this->getMockBuilder(OrderAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Payment $paymentInfoMock */
        $paymentInfoMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentInfoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->orderAdapterFactoryMock->expects($this->once())
            ->method('create')
            ->with(['order' => $orderMock])
            ->willReturn($orderAdapterMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                PaymentDataObject::class,
                [
                    'order' => $orderAdapterMock,
                    'payment' => $paymentInfoMock
                ]
            )->willReturn($this->paymentDataObjectMock);

        $this->assertSame($this->paymentDataObjectMock, $this->model->create($paymentInfoMock));
    }

    public function testCreatePaymentDataObjectFromQuote()
    {
        /** @var Quote $quoteMock */
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var OrderAdapter $orderAdapterMock */
        $quoteAdapterMock = $this->getMockBuilder(QuoteAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Quote\Payment $paymentInfoMock */
        $paymentInfoMock = $this->getMockBuilder(Quote\Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentInfoMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $this->quoteAdapterFactoryMock->expects($this->once())
            ->method('create')
            ->with(['quote' => $quoteMock])
            ->willReturn($quoteAdapterMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                PaymentDataObject::class,
                [
                    'order' => $quoteAdapterMock,
                    'payment' => $paymentInfoMock
                ]
            )->willReturn($this->paymentDataObjectMock);

        $this->assertSame($this->paymentDataObjectMock, $this->model->create($paymentInfoMock));
    }
}
