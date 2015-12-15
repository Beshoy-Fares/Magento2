<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BraintreeTwo\Test\Unit\Gateway\Request;

use Magento\BraintreeTwo\Gateway\Request\CaptureDataBuilder;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CaptureDataBuilderTest
 */
class CaptureDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BraintreeTwo\Gateway\Request\CaptureDataBuilder
     */
    private $builder;

    /**
     * @var Payment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $payment;

    /**
     * @var \Magento\Sales\Model\Order\Payment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $paymentDO;

    protected function setUp()
    {
        $this->paymentDO = $this->getMock(PaymentDataObjectInterface::class);
        $this->payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder = new CaptureDataBuilder();
    }

    /**
     * @covers \Magento\BraintreeTwo\Gateway\Request\CaptureDataBuilder::build
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No authorization transaction to proceed capture.
     */
    public function testBuildWithException()
    {
        $amount = 10.00;
        $buildSubject = [
            'payment' => $this->paymentDO,
            'amount' => $amount
        ];

        $this->payment->expects(static::once())
            ->method('getCcTransId')
            ->willReturn('');

        $this->paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->payment);

        $this->builder->build($buildSubject);
    }

    /**
     * @covers \Magento\BraintreeTwo\Gateway\Request\CaptureDataBuilder::build
     */
    public function testBuild()
    {
        $transactionId = 'b3b99d';
        $amount = 10.00;

        $expected = [
            'transaction_id' => $transactionId,
            'amount' => $amount
        ];

        $buildSubject = [
            'payment' => $this->paymentDO,
            'amount' => $amount
        ];

        $this->payment->expects(static::once())
            ->method('getCcTransId')
            ->willReturn($transactionId);

        $this->paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->payment);

        static::assertEquals($expected, $this->builder->build($buildSubject));
    }
}
