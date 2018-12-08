<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
declare(strict_types=1);

namespace Magento\Braintree\Test\Unit\Gateway\Http\Client;

use Braintree\Result\Successful;
use Magento\Braintree\Gateway\Http\Client\TransactionRefund;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Model\Adapter\BraintreeAdapterFactory;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Braintree\Gateway\Request\PaymentDataBuilder;
use Magento\Payment\Model\Method\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class TransactionRefundTest
=======
namespace Magento\Braintree\Test\Unit\Gateway\Http\Client;

use Magento\Braintree\Gateway\Http\Client\TransactionRefund;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Model\Adapter\BraintreeAdapterFactory;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;
use Magento\Braintree\Gateway\Request\PaymentDataBuilder;

/**
 * Tests \Magento\Braintree\Gateway\Http\Client\TransactionRefund.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class TransactionRefundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TransactionRefund
     */
<<<<<<< HEAD
    private $transactionRefundModel;

    /**
     * @var BraintreeAdapter|\PHPUnit_Framework_MockObject_MockObject
=======
    private $client;

    /**
     * @var Logger|MockObject
     */
    private $loggerMock;

    /**
     * @var BraintreeAdapter|MockObject
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     */
    private $adapterMock;

    /**
<<<<<<< HEAD
=======
     * @var string
     */
    private $transactionId = 'px4kpev5';

    /**
     * @var string
     */
    private $paymentAmount = '100.00';

    /**
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @inheritdoc
     */
    protected function setUp()
    {
        /** @var LoggerInterface|MockObject $criticalLoggerMock */
        $criticalLoggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
<<<<<<< HEAD
        /** @var Logger|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(Logger::class)
=======
        $this->loggerMock = $this->getMockBuilder(Logger::class)
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->disableOriginalConstructor()
            ->getMock();
        $this->adapterMock = $this->getMockBuilder(BraintreeAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var BraintreeAdapterFactory|MockObject $adapterFactoryMock */
        $adapterFactoryMock = $this->getMockBuilder(BraintreeAdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
<<<<<<< HEAD
        $adapterFactoryMock->method('create')
            ->willReturn($this->adapterMock);

        $this->transactionRefundModel = new TransactionRefund($criticalLoggerMock, $loggerMock, $adapterFactoryMock);
    }

    /**
     * @throws ClientException
     * @throws ConverterException
     */
    public function testRefundRequestWithStoreId()
    {
        $transactionId = '11223344';
        $refundAmount = 10;
        $data = [
            'store_id' => 0,
            'transaction_id' => $transactionId,
            PaymentDataBuilder::AMOUNT => $refundAmount
        ];
        $successfulResponse = new Successful();

        /** @var TransferInterface|\PHPUnit_Framework_MockObject_MockObject $transferObjectMock */
        $transferObjectMock = $this->createMock(TransferInterface::class);
        $transferObjectMock->method('getBody')
            ->willReturn($data);
        $this->adapterMock->expects($this->once())
            ->method('refund')
            ->with($transactionId, $refundAmount)
            ->willReturn($successfulResponse);

        $response = $this->transactionRefundModel->placeRequest($transferObjectMock);

        self::assertEquals($successfulResponse, $response['object']);
=======
        $adapterFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->adapterMock);

        $this->client = new TransactionRefund($criticalLoggerMock, $this->loggerMock, $adapterFactoryMock);
    }

    /**
     * @return void
     *
     * @expectedException \Magento\Payment\Gateway\Http\ClientException
     * @expectedExceptionMessage Test messages
     */
    public function testPlaceRequestException()
    {
        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with(
                [
                    'request' => $this->getTransferData(),
                    'client' => TransactionRefund::class,
                    'response' => [],
                ]
            );

        $this->adapterMock->expects($this->once())
            ->method('refund')
            ->with($this->transactionId, $this->paymentAmount)
            ->willThrowException(new \Exception('Test messages'));

        /** @var TransferInterface|MockObject $transferObjectMock */
        $transferObjectMock = $this->getTransferObjectMock();

        $this->client->placeRequest($transferObjectMock);
    }

    /**
     * @return void
     */
    public function testPlaceRequestSuccess()
    {
        $response = new \stdClass;
        $response->success = true;
        $this->adapterMock->expects($this->once())
            ->method('refund')
            ->with($this->transactionId, $this->paymentAmount)
            ->willReturn($response);

        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with(
                [
                    'request' => $this->getTransferData(),
                    'client' => TransactionRefund::class,
                    'response' => ['success' => 1],
                ]
            );

        $actualResult = $this->client->placeRequest($this->getTransferObjectMock());

        $this->assertTrue(is_object($actualResult['object']));
        $this->assertEquals(['object' => $response], $actualResult);
    }

    /**
     * Creates mock object for TransferInterface.
     *
     * @return TransferInterface|MockObject
     */
    private function getTransferObjectMock()
    {
        $transferObjectMock = $this->createMock(TransferInterface::class);
        $transferObjectMock->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getTransferData());

        return $transferObjectMock;
    }

    /**
     * Creates stub request data.
     *
     * @return array
     */
    private function getTransferData()
    {
        return [
            'transaction_id' => $this->transactionId,
            PaymentDataBuilder::AMOUNT => $this->paymentAmount,
        ];
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    }
}
