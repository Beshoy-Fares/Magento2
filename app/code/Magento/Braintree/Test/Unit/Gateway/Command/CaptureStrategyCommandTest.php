<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Test\Unit\Gateway\Command;

use Braintree\IsNode;
use Magento\Braintree\Gateway\Command\CaptureStrategyCommand;
use Magento\Braintree\Gateway\SubjectReader;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Model\Adapter\BraintreeAdapterFactory;
use Magento\Braintree\Model\Adapter\BraintreeSearchAdapter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Command\GatewayCommand;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests \Magento\Braintree\Gateway\Command\CaptureStrategyCommand.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CaptureStrategyCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CaptureStrategyCommand
     */
    private $strategyCommand;

    /**
     * @var CommandPoolInterface|MockObject
     */
    private $commandPoolMock;

    /**
     * @var TransactionRepositoryInterface|MockObject
     */
    private $transactionRepositoryMock;

    /**
     * @var FilterBuilder|MockObject
     */
    private $filterBuilderMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Payment|MockObject
     */
    private $paymentMock;

    /**
     * @var GatewayCommand|MockObject
     */
    private $commandMock;

    /**
     * @var SubjectReader|MockObject
     */
    private $subjectReader;

    /**
     * @var BraintreeAdapter|MockObject
     */
    private $braintreeAdapterMock;

    /**
     * @var BraintreeSearchAdapter
     */
    private $braintreeSearchAdapter;

    protected function setUp()
    {
        $this->commandPoolMock = $this->getMockBuilder(CommandPoolInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', '__wakeup'])
            ->getMock();

        $this->subjectReader = $this->getMockBuilder(SubjectReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->initCommandMock();
        $this->initTransactionRepositoryMock();
        $this->initFilterBuilderMock();
        $this->initSearchCriteriaBuilderMock();

        $this->braintreeAdapterMock = $this->getMockBuilder(BraintreeAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var BraintreeAdapterFactory|MockObject $adapterFactoryMock */
        $adapterFactoryMock = $this->getMockBuilder(BraintreeAdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
<<<<<<< HEAD
        $adapterFactoryMock->expects(self::any())
            ->method('create')
            ->willReturn($this->braintreeAdapterMock);
=======
        /** @var BraintreeAdapterFactory|MockObject $adapterFactory */
        $adapterFactory = $this->getMockBuilder(BraintreeAdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactory->method('create')
            ->willReturn($this->braintreeAdapter);
>>>>>>> upstream/2.2-develop

        $this->braintreeSearchAdapter = new BraintreeSearchAdapter();

        $this->strategyCommand = new CaptureStrategyCommand(
<<<<<<< HEAD
            $this->commandPoolMock,
            $this->transactionRepositoryMock,
            $this->filterBuilderMock,
            $this->searchCriteriaBuilderMock,
            $this->subjectReaderMock,
            $adapterFactoryMock,
=======
            $this->commandPool,
            $this->transactionRepository,
            $this->filterBuilder,
            $this->searchCriteriaBuilder,
            $this->subjectReader,
            $adapterFactory,
>>>>>>> upstream/2.2-develop
            $this->braintreeSearchAdapter
        );
    }

    public function testSaleExecute()
    {
        $paymentData = $this->getPaymentDataObjectMock();
        $subject['payment'] = $paymentData;

        $this->subjectReader->method('readPayment')
            ->with($subject)
            ->willReturn($paymentData);

<<<<<<< HEAD
        $this->paymentMock->expects(static::once())
            ->method('getAuthorizationTransaction')
            ->willReturn(false);

        $this->paymentMock->expects(static::once())
            ->method('getId')
=======
        $this->payment->method('getAuthorizationTransaction')
            ->willReturn(false);

        $this->payment->method('getId')
>>>>>>> upstream/2.2-develop
            ->willReturn(1);

        $this->buildSearchCriteria();

<<<<<<< HEAD
        $this->transactionRepositoryMock->expects(static::once())
            ->method('getTotalCount')
            ->willReturn(0);

        $this->commandPoolMock->expects(static::once())
            ->method('get')
=======
        $this->transactionRepository->method('getTotalCount')
            ->willReturn(0);

        $this->commandPool->method('get')
>>>>>>> upstream/2.2-develop
            ->with(CaptureStrategyCommand::SALE)
            ->willReturn($this->commandMock);

        $this->strategyCommand->execute($subject);
    }

    public function testCaptureExecute()
    {
        $paymentData = $this->getPaymentDataObjectMock();
        $subject['payment'] = $paymentData;
        $lastTransId = 'txnds';

        $this->subjectReader->method('readPayment')
            ->with($subject)
            ->willReturn($paymentData);

<<<<<<< HEAD
        $this->paymentMock->expects(static::once())
            ->method('getAuthorizationTransaction')
            ->willReturn(true);
        $this->paymentMock->expects(static::once())
            ->method('getLastTransId')
            ->willReturn($lastTransId);

        $this->paymentMock->expects(static::once())
            ->method('getId')
=======
        $this->payment->method('getAuthorizationTransaction')
            ->willReturn(true);
        $this->payment->method('getLastTransId')
            ->willReturn($lastTransId);

        $this->payment->method('getId')
>>>>>>> upstream/2.2-develop
            ->willReturn(1);

        $this->buildSearchCriteria();

<<<<<<< HEAD
        $this->transactionRepositoryMock->expects(static::once())
            ->method('getTotalCount')
=======
        $this->transactionRepository->method('getTotalCount')
>>>>>>> upstream/2.2-develop
            ->willReturn(0);

        // authorization transaction was not expired
        $collection = $this->getNotExpiredExpectedCollection($lastTransId);
        $collection->method('maximumCount')
            ->willReturn(0);

<<<<<<< HEAD
        $this->commandPoolMock->expects(static::once())
            ->method('get')
=======
        $this->commandPool->method('get')
>>>>>>> upstream/2.2-develop
            ->with(CaptureStrategyCommand::CAPTURE)
            ->willReturn($this->commandMock);

        $this->strategyCommand->execute($subject);
    }

    /**
     * @param string $lastTransactionId
     * @return \Braintree\ResourceCollection|MockObject
     */
    private function getNotExpiredExpectedCollection($lastTransactionId)
    {
        $isExpectations = [
            'id' => ['is' => $lastTransactionId],
            'status' => [\Braintree\Transaction::AUTHORIZATION_EXPIRED]
        ];

        $collection = $this->getMockBuilder(\Braintree\ResourceCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

<<<<<<< HEAD
        $this->braintreeAdapterMock->expects(static::once())
            ->method('search')
=======
        $this->braintreeAdapter->method('search')
>>>>>>> upstream/2.2-develop
            ->with(
                self::callback(
                    function (array $filters) use ($isExpectations) {
                        foreach ($filters as $filter) {
                            /** @var IsNode $filter */
                            if (!isset($isExpectations[$filter->name])) {
                                return false;
                            }

                            if ($isExpectations[$filter->name] !== $filter->toParam()) {
                                return false;
                            }
                        }

                        return true;
                    }
                )
            )
            ->willReturn($collection);

        return $collection;
    }

    public function testExpiredAuthorizationPerformVaultCaptureExecute()
    {
        $paymentData = $this->getPaymentDataObjectMock();
        $subject['payment'] = $paymentData;
        $lastTransId = 'txnds';

        $this->subjectReader->method('readPayment')
            ->with($subject)
            ->willReturn($paymentData);

<<<<<<< HEAD
        $this->paymentMock->expects(static::once())
            ->method('getAuthorizationTransaction')
            ->willReturn(true);
        $this->paymentMock->expects(static::once())
            ->method('getLastTransId')
            ->willReturn($lastTransId);

        $this->paymentMock->expects(static::once())
            ->method('getId')
=======
        $this->payment->method('getAuthorizationTransaction')
            ->willReturn(true);
        $this->payment->method('getLastTransId')
            ->willReturn($lastTransId);

        $this->payment->method('getId')
>>>>>>> upstream/2.2-develop
            ->willReturn(1);

        $this->buildSearchCriteria();

<<<<<<< HEAD
        $this->transactionRepositoryMock->expects(static::once())
            ->method('getTotalCount')
=======
        $this->transactionRepository->method('getTotalCount')
>>>>>>> upstream/2.2-develop
            ->willReturn(0);

        // authorization transaction was expired
        $collection = $this->getNotExpiredExpectedCollection($lastTransId);
        $collection->method('maximumCount')
            ->willReturn(1);

<<<<<<< HEAD
        $this->commandPoolMock->expects(static::once())
            ->method('get')
=======
        $this->commandPool->method('get')
>>>>>>> upstream/2.2-develop
            ->with(CaptureStrategyCommand::VAULT_CAPTURE)
            ->willReturn($this->commandMock);

        $this->strategyCommand->execute($subject);
    }

    public function testVaultCaptureExecute()
    {
        $paymentData = $this->getPaymentDataObjectMock();
        $subject['payment'] = $paymentData;

        $this->subjectReader->method('readPayment')
            ->with($subject)
            ->willReturn($paymentData);

<<<<<<< HEAD
        $this->paymentMock->expects(static::once())
            ->method('getAuthorizationTransaction')
            ->willReturn(true);

        $this->paymentMock->expects(static::once())
            ->method('getId')
=======
        $this->payment->method('getAuthorizationTransaction')
            ->willReturn(true);

        $this->payment->method('getId')
>>>>>>> upstream/2.2-develop
            ->willReturn(1);

        $this->buildSearchCriteria();

<<<<<<< HEAD
        $this->transactionRepositoryMock->expects(static::once())
            ->method('getTotalCount')
            ->willReturn(1);

        $this->commandPoolMock->expects(static::once())
            ->method('get')
=======
        $this->transactionRepository->method('getTotalCount')
            ->willReturn(1);

        $this->commandPool->method('get')
>>>>>>> upstream/2.2-develop
            ->with(CaptureStrategyCommand::VAULT_CAPTURE)
            ->willReturn($this->commandMock);

        $this->strategyCommand->execute($subject);
    }

    /**
     * Creates mock for payment data object and order payment
     * @return MockObject
     */
    private function getPaymentDataObjectMock()
    {
        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock = $this->getMockBuilder(PaymentDataObject::class)
            ->setMethods(['getPayment', 'getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

<<<<<<< HEAD
        $mock->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->paymentMock);

        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getOrder')
            ->willReturn($orderMock);
=======
        $mock->method('getPayment')
            ->willReturn($this->payment);
>>>>>>> upstream/2.2-develop

        $order = $this->getMockBuilder(OrderAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getOrder')
            ->willReturn($order);

        return $mock;
    }

    /**
     * Creates mock for gateway command object
     */
    private function initCommandMock()
    {
        $this->commandMock = $this->getMockBuilder(GatewayCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

<<<<<<< HEAD
        $this->commandMock->expects(static::once())
            ->method('execute')
=======
        $this->command->method('execute')
>>>>>>> upstream/2.2-develop
            ->willReturn([]);
    }

    /**
     * Creates mock for filter object
     */
    private function initFilterBuilderMock()
    {
        $this->filterBuilderMock = $this->getMockBuilder(FilterBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setField', 'setValue', 'create', '__wakeup'])
            ->getMock();
    }

    /**
     * Builds search criteria
     */
    private function buildSearchCriteria()
    {
<<<<<<< HEAD
        $this->filterBuilderMock->expects(static::exactly(2))
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilderMock->expects(static::exactly(2))
=======
        $this->filterBuilder->expects(self::exactly(2))
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects(self::exactly(2))
>>>>>>> upstream/2.2-develop
            ->method('setValue')
            ->willReturnSelf();

        $searchCriteria = new SearchCriteria();
<<<<<<< HEAD
        $this->searchCriteriaBuilderMock->expects(static::exactly(2))
            ->method('addFilters')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects(static::once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->transactionRepositoryMock->expects(static::once())
            ->method('getList')
=======
        $this->searchCriteriaBuilder->expects(self::exactly(2))
            ->method('addFilters')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->method('create')
            ->willReturn($searchCriteria);

        $this->transactionRepository->method('getList')
>>>>>>> upstream/2.2-develop
            ->with($searchCriteria)
            ->willReturnSelf();
    }

    /**
     * Create mock for search criteria object
     */
    private function initSearchCriteriaBuilderMock()
    {
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addFilters', 'create', '__wakeup'])
            ->getMock();
    }

    /**
     * Create mock for transaction repository
     */
    private function initTransactionRepositoryMock()
    {
        $this->transactionRepositoryMock = $this->getMockBuilder(TransactionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList', 'getTotalCount', 'delete', 'get', 'save', 'create', '__wakeup'])
            ->getMock();
    }
}
