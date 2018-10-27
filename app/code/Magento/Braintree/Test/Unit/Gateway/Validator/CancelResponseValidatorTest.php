<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Braintree\Test\Unit\Gateway\Validator;

use Braintree\Result\Error;
use Magento\Braintree\Gateway\Validator\CancelResponseValidator;
use PHPUnit\Framework\TestCase;
use Magento\Braintree\Gateway\Validator\GeneralResponseValidator;
use Magento\Braintree\Gateway\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

<<<<<<< HEAD
/**
 * Tests \Magento\Braintree\Gateway\Validator\CancelResponseValidator class.
 */
=======
>>>>>>> upstream/2.2-develop
class CancelResponseValidatorTest extends TestCase
{
    /**
     * @var CancelResponseValidator
     */
    private $validator;

    /**
     * @var GeneralResponseValidator|MockObject
     */
    private $generalValidator;

    /**
     * @var ResultInterfaceFactory|MockObject
     */
    private $resultFactory;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->generalValidator = $this->getMockBuilder(GeneralResponseValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactory = $this->getMockBuilder(ResultInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new CancelResponseValidator(
            $this->resultFactory,
            $this->generalValidator,
            new SubjectReader()
        );
    }

    /**
     * Checks a case when response is successful and additional validation doesn't needed.
<<<<<<< HEAD
     *
     * @return void
     */
    public function testValidateSuccessfulTransaction(): void
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);
        $result->method('isValid')->willReturn(true);
        $this->generalValidator->method('validate')->willReturn($result);
        $actual = $this->validator->validate([]);

        $this->assertSame($result, $actual);
=======
     */
    public function testValidateSuccessfulTransaction()
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);$result->method('isValid')
            ->willReturn(true);

        $this->generalValidator->method('validate')
            ->willReturn($result);

        $actual = $this->validator->validate([]);
        self::assertSame($result, $actual);
>>>>>>> upstream/2.2-develop
    }

    /**
     * Checks a case when response contains error related to expired authorization transaction and
     * validator should return positive result.
<<<<<<< HEAD
     *
     * @return void
     */
    public function testValidateExpiredTransaction(): void
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);
        $result->method('isValid')->willReturn(false);
        $this->generalValidator->method('validate')->willReturn($result);

        $expected = $this->getMockForAbstractClass(ResultInterface::class);
        $expected->method('isValid')->willReturn(true);
        $this->resultFactory->method('create')
            ->with(
                [
                    'isValid' => true,
                    'failsDescription' => ['Transaction is cancelled offline.'],
                    'errorCodes' => []
                ]
            )->willReturn($expected);
=======
     */
    public function testValidateExpiredTransaction()
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);
        $result->method('isValid')
            ->willReturn(false);

        $this->generalValidator->method('validate')
            ->willReturn($result);

        $expected = $this->getMockForAbstractClass(ResultInterface::class);
        $expected->method('isValid')
            ->willReturn(true);
        $this->resultFactory->method('create')
            ->with(['isValid' => true, 'failsDescription' => ['Transaction is cancelled offline.']])
            ->willReturn($expected);
>>>>>>> upstream/2.2-develop

        $errors = [
            'errors' => [
                [
                    'code' => 91504,
                    'message' => 'Transaction can only be voided if status is authorized.',
<<<<<<< HEAD
                ],
            ],
        ];
        $buildSubject = [
            'response' => [
                'object' => new Error(['errors' => $errors]),
            ],
        ];

        $actual = $this->validator->validate($buildSubject);

        $this->assertSame($expected, $actual);
=======
                ]
            ]
        ];
        $buildSubject = [
            'response' => [
                'object' => new Error(['errors' => $errors])
            ]
        ];

        $actual = $this->validator->validate($buildSubject);
        self::assertSame($expected, $actual);
>>>>>>> upstream/2.2-develop
    }

    /**
     * Checks a case when response contains multiple errors and validator should return negative result.
     *
     * @param array $responseErrors
<<<<<<< HEAD
     * @return void
     * @dataProvider getErrorsDataProvider
     */
    public function testValidateWithMultipleErrors(array $responseErrors): void
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);
        $result->method('isValid')->willReturn(false);

        $this->generalValidator->method('validate')->willReturn($result);

        $this->resultFactory->expects($this->never())->method('create');

        $errors = [
            'errors' => $responseErrors,
        ];
        $buildSubject = [
            'response' => [
                'object' => new Error(['errors' => $errors]),
=======
     * @dataProvider getErrorsDataProvider
     */
    public function testValidateWithMultipleErrors(array $responseErrors)
    {
        /** @var ResultInterface|MockObject $result */
        $result = $this->getMockForAbstractClass(ResultInterface::class);
        $result->method('isValid')
            ->willReturn(false);

        $this->generalValidator->method('validate')
            ->willReturn($result);

        $this->resultFactory->expects(self::never())
            ->method('create');

        $errors = [
            'errors' => $responseErrors
        ];
        $buildSubject = [
            'response' => [
                'object' => new Error(['errors' => $errors])
>>>>>>> upstream/2.2-develop
            ]
        ];

        $actual = $this->validator->validate($buildSubject);
<<<<<<< HEAD

        $this->assertSame($result, $actual);
=======
        self::assertSame($result, $actual);
>>>>>>> upstream/2.2-develop
    }

    /**
     * Gets list of errors variations.
     *
     * @return array
     */
    public function getErrorsDataProvider(): array
    {
        return [
            [
                'errors' => [
                    [
                        'code' => 91734,
                        'message' => 'Credit card type is not accepted by this merchant account.',
                    ],
                    [
                        'code' => 91504,
                        'message' => 'Transaction can only be voided if status is authorized.',
<<<<<<< HEAD
                    ],
                ],
=======
                    ]
                ]
>>>>>>> upstream/2.2-develop
            ],
            [
                'errors' => [
                    [
                        'code' => 91734,
                        'message' => 'Credit card type is not accepted by this merchant account.',
                    ],
<<<<<<< HEAD
                ],
            ],
=======
                ]
            ]
>>>>>>> upstream/2.2-develop
        ];
    }
}
