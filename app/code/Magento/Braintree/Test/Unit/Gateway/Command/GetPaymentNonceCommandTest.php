<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Test\Unit\Gateway\Command;

use Magento\Braintree\Gateway\Command\GetPaymentNonceCommand;
use Magento\Braintree\Gateway\SubjectReader;
use Magento\Braintree\Gateway\Validator\PaymentNonceResponseValidator;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Model\Adapter\BraintreeAdapterFactory;
use Magento\Payment\Gateway\Command\Result\ArrayResult;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Vault\Model\PaymentToken;
use Magento\Vault\Model\PaymentTokenManagement;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class GetPaymentNonceCommandTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GetPaymentNonceCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GetPaymentNonceCommand
     */
    private $command;

    /**
     * @var BraintreeAdapter|MockObject
     */
    private $adapterMock;

    /**
     * @var PaymentTokenManagement|MockObject
     */
    private $tokenManagementMock;

    /**
     * @var PaymentToken|MockObject
     */
    private $paymentTokenMock;

    /**
     * @var ArrayResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var SubjectReader|MockObject
     */
    private $subjectReaderMock;

    /**
     * @var PaymentNonceResponseValidator|MockObject
     */
    private $responseValidatorMock;

    /**
     * @var ResultInterface|MockObject
     */
    private $validationResultMock;

    protected function setUp()
    {
        $this->paymentTokenMock = $this->getMockBuilder(PaymentToken::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGatewayToken'])
            ->getMock();

        $this->tokenManagementMock = $this->getMockBuilder(PaymentTokenManagement::class)
            ->disableOriginalConstructor()
            ->setMethods(['getByPublicHash'])
            ->getMock();

        $this->adapterMock = $this->getMockBuilder(BraintreeAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNonce'])
            ->getMock();
<<<<<<< HEAD
        /** @var BraintreeAdapterFactory|MockObject $adapterFactory */
        $adapterFactory = $this->getMockBuilder(BraintreeAdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactory->method('create')
            ->willReturn($this->adapter);
=======
        /** @var BraintreeAdapterFactory|MockObject $adapterFactoryMock */
        $adapterFactoryMock = $this->getMockBuilder(BraintreeAdapterFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapterFactoryMock->expects(self::any())
            ->method('create')
            ->willReturn($this->adapterMock);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        $this->resultFactoryMock = $this->getMockBuilder(ArrayResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->subjectReaderMock = $this->getMockBuilder(SubjectReader::class)
            ->disableOriginalConstructor()
            ->setMethods(['readPublicHash', 'readCustomerId'])
            ->getMock();

        $this->validationResultMock = $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['isValid', 'getFailsDescription', 'getErrorCodes'])
            ->getMock();

        $this->responseValidatorMock = $this->getMockBuilder(PaymentNonceResponseValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validate', 'isValid', 'getFailsDescription'])
            ->getMock();

        $this->command = new GetPaymentNonceCommand(
<<<<<<< HEAD
            $this->tokenManagement,
            $adapterFactory,
            $this->resultFactory,
            $this->subjectReader,
            $this->responseValidator
=======
            $this->tokenManagementMock,
            $adapterFactoryMock,
            $this->resultFactoryMock,
            $this->subjectReaderMock,
            $this->responseValidatorMock
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The "publicHash" field does not exists
     */
    public function testExecuteWithExceptionForPublicHash()
    {
        $exception = new \InvalidArgumentException('The "publicHash" field does not exists');

<<<<<<< HEAD
        $this->subjectReader->method('readPublicHash')
            ->willThrowException($exception);

        $this->subjectReader->expects(self::never())
=======
        $this->subjectReaderMock->expects(static::once())
            ->method('readPublicHash')
            ->willThrowException($exception);

        $this->subjectReaderMock->expects(self::never())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('readCustomerId');

        $this->command->execute([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The "customerId" field does not exists
     */
    public function testExecuteWithExceptionForCustomerId()
    {
        $publicHash = '3wv2m24d2er3';

<<<<<<< HEAD
        $this->subjectReader->method('readPublicHash')
            ->willReturn($publicHash);

        $exception = new \InvalidArgumentException('The "customerId" field does not exists');
        $this->subjectReader->method('readCustomerId')
            ->willThrowException($exception);

        $this->tokenManagement->expects(self::never())
=======
        $this->subjectReaderMock->expects(static::once())
            ->method('readPublicHash')
            ->willReturn($publicHash);

        $exception = new \InvalidArgumentException('The "customerId" field does not exists');
        $this->subjectReaderMock->expects(static::once())
            ->method('readCustomerId')
            ->willThrowException($exception);

        $this->tokenManagementMock->expects(static::never())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('getByPublicHash');

        $this->command->execute(['publicHash' => $publicHash]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No available payment tokens
     */
    public function testExecuteWithExceptionForTokenManagement()
    {
        $publicHash = '3wv2m24d2er3';
        $customerId = 1;

<<<<<<< HEAD
        $this->subjectReader->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReader->method('readCustomerId')
            ->willReturn($customerId);

        $exception = new \Exception('No available payment tokens');
        $this->tokenManagement->method('getByPublicHash')
            ->willThrowException($exception);

        $this->paymentToken->expects(self::never())
=======
        $this->subjectReaderMock->expects(static::once())
            ->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReaderMock->expects(static::once())
            ->method('readCustomerId')
            ->willReturn($customerId);

        $exception = new \Exception('No available payment tokens');
        $this->tokenManagementMock->expects(static::once())
            ->method('getByPublicHash')
            ->willThrowException($exception);

        $this->paymentTokenMock->expects(self::never())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('getGatewayToken');

        $this->command->execute(['publicHash' => $publicHash, 'customerId' => $customerId]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Payment method nonce can't be retrieved.
     */
    public function testExecuteWithFailedValidation()
    {
        $publicHash = '3wv2m24d2er3';
        $customerId = 1;
        $token = 'jd2vnq';

<<<<<<< HEAD
        $this->subjectReader->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReader->method('readCustomerId')
            ->willReturn($customerId);

        $this->tokenManagement->method('getByPublicHash')
=======
        $this->subjectReaderMock->expects(static::once())
            ->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReaderMock->expects(static::once())
            ->method('readCustomerId')
            ->willReturn($customerId);

        $this->tokenManagementMock->expects(static::once())
            ->method('getByPublicHash')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->with($publicHash, $customerId)
            ->willReturn($this->paymentTokenMock);

<<<<<<< HEAD
        $this->paymentToken->method('getGatewayToken')
=======
        $this->paymentTokenMock->expects(static::once())
            ->method('getGatewayToken')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->willReturn($token);

        $obj = new \stdClass();
        $obj->success = false;
<<<<<<< HEAD
        $this->adapter->method('createNonce')
            ->with($token)
            ->willReturn($obj);

        $this->responseValidator->method('validate')
=======
        $this->adapterMock->expects(static::once())
            ->method('createNonce')
            ->with($token)
            ->willReturn($obj);

        $this->responseValidatorMock->expects(static::once())
            ->method('validate')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->with(['response' => ['object' => $obj]])
            ->willReturn($this->validationResultMock);

<<<<<<< HEAD
        $this->validationResult->method('isValid')
            ->willReturn(false);

        $this->validationResult->method('getFailsDescription')
            ->willReturn(['Payment method nonce can\'t be retrieved.']);

        $this->resultFactory->expects(self::never())
=======
        $this->validationResultMock->expects(static::once())
            ->method('isValid')
            ->willReturn(false);

        $this->validationResultMock->expects(static::once())
            ->method('getFailsDescription')
            ->willReturn(['Payment method nonce can\'t be retrieved.']);

        $this->resultFactoryMock->expects(static::never())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('create');

        $this->command->execute(['publicHash' => $publicHash, 'customerId' => $customerId]);
    }

    public function testExecute()
    {
        $publicHash = '3wv2m24d2er3';
        $customerId = 1;
        $token = 'jd2vnq';
        $nonce = 's1dj23';

<<<<<<< HEAD
        $this->subjectReader->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReader->method('readCustomerId')
            ->willReturn($customerId);

        $this->tokenManagement->method('getByPublicHash')
=======
        $this->subjectReaderMock->expects(static::once())
            ->method('readPublicHash')
            ->willReturn($publicHash);

        $this->subjectReaderMock->expects(static::once())
            ->method('readCustomerId')
            ->willReturn($customerId);

        $this->tokenManagementMock->expects(static::once())
            ->method('getByPublicHash')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->with($publicHash, $customerId)
            ->willReturn($this->paymentTokenMock);

<<<<<<< HEAD
        $this->paymentToken->method('getGatewayToken')
=======
        $this->paymentTokenMock->expects(static::once())
            ->method('getGatewayToken')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->willReturn($token);

        $obj = new \stdClass();
        $obj->success = true;
        $obj->paymentMethodNonce = new \stdClass();
        $obj->paymentMethodNonce->nonce = $nonce;
<<<<<<< HEAD
        $this->adapter->method('createNonce')
            ->with($token)
            ->willReturn($obj);

        $this->responseValidator->method('validate')
=======
        $this->adapterMock->expects(static::once())
            ->method('createNonce')
            ->with($token)
            ->willReturn($obj);

        $this->responseValidatorMock->expects(static::once())
            ->method('validate')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->with(['response' => ['object' => $obj]])
            ->willReturn($this->validationResultMock);

<<<<<<< HEAD
        $this->validationResult->method('isValid')
            ->willReturn(true);

        $this->validationResult->expects(self::never())
=======
        $this->validationResultMock->expects(static::once())
            ->method('isValid')
            ->willReturn(true);

        $this->validationResultMock->expects(self::never())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('getFailsDescription');

        $expected = $this->getMockBuilder(ArrayResult::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $expected->method('get')
            ->willReturn(['paymentMethodNonce' => $nonce]);
<<<<<<< HEAD
        $this->resultFactory->method('create')
=======
        $this->resultFactoryMock->expects(static::once())
            ->method('create')
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->willReturn($expected);

        $actual = $this->command->execute(['publicHash' => $publicHash, 'customerId' => $customerId]);
        self::assertEquals($expected, $actual);
        self::assertEquals($nonce, $actual->get()['paymentMethodNonce']);
    }
}
