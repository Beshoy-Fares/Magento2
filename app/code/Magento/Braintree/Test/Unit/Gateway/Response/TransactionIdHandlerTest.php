<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Test\Unit\Gateway\Response;

use Magento\Braintree\Gateway\SubjectReader;
use Magento\Braintree\Gateway\Response\TransactionIdHandler;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

class TransactionIdHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $paymentDO = $this->createMock(PaymentDataObjectInterface::class);
        $paymentInfo = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $handlingSubject = [
            'payment' => $paymentDO
        ];

        $transaction = \Braintree\Transaction::factory(['id' => 1]);
        $response = [
            'object' => new \Braintree\Result\Successful($transaction, 'transaction')
        ];

        $subjectReader = $this->getMockBuilder(SubjectReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subjectReader->expects($this->once())
            ->method('readPayment')
            ->with($handlingSubject)
            ->willReturn($paymentDO);
        $paymentDO->expects($this->atLeastOnce())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $subjectReader->expects($this->once())
            ->method('readTransaction')
            ->with($response)
            ->willReturn($transaction);

        $paymentInfo->expects($this->once())
            ->method('setTransactionId')
            ->with(1);

        $paymentInfo->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with(false);
        $paymentInfo->expects($this->once())
            ->method('setShouldCloseParentTransaction')
            ->with(false);

        $handler = new TransactionIdHandler($subjectReader);
        $handler->handle($handlingSubject, $response);
    }
}
