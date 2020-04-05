<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Test\Unit\Gateway\Validator;

use Magento\Framework\Phrase;
use Magento\Payment\Gateway\Validator\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    private $model;

    /**
     * @param $isValid mixed
     * @param $failsDescription array
     * @param $expectedIsValid mixed
     * @param $expectedFailsDescription array
     * @dataProvider resultDataProvider
     */
    public function testResult($isValid, $failsDescription, $expectedIsValid, $expectedFailsDescription)
    {
        $this->model = new Result($isValid, $failsDescription);
        $this->assertEquals($expectedIsValid, $this->model->isValid());
        $this->assertEquals($expectedFailsDescription, $this->model->getFailsDescription());
    }

    /**
     * @return array
     */
    public function resultDataProvider()
    {
        $phraseMock = $this->getMockBuilder(Phrase::class)->disableOriginalConstructor()->getMock();
        return [
            [true, [$phraseMock, $phraseMock], true, [$phraseMock, $phraseMock]],
            ['', [], false, []],
        ];
    }
}
