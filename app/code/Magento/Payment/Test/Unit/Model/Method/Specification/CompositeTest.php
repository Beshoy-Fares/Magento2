<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model\Method\Specification;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Model\Method\Specification\Composite;
use Magento\Payment\Model\Method\Specification\Factory;
use Magento\Payment\Model\Method\SpecificationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CompositeTest extends TestCase
{
    /**
     * @var Factory|MockObject
     */
    private $factoryMock;

    protected function setUp()
    {
        $this->factoryMock = $this->createMock(Factory::class);
    }

    private function createComposite($specifications = [])
    {
        $objectManager = new ObjectManager($this);

        return $objectManager->getObject(
            Composite::class,
            ['factory' => $this->factoryMock, 'specifications' => $specifications]
        );
    }

    /**
     * @param bool $firstSpecificationResult
     * @param bool $secondSpecificationResult
     * @param bool $compositeResult
     * @dataProvider compositeDataProvider
     */
    public function testComposite($firstSpecificationResult, $secondSpecificationResult, $compositeResult)
    {
        $method = 'method-name';

        $specificationFirst = $this->createMock(SpecificationInterface::class);
        $specificationFirst->expects(
            $this->once()
        )->method(
            'isSatisfiedBy'
        )->with(
            $method
        )->will(
            $this->returnValue($firstSpecificationResult)
        );

        $specificationSecond = $this->createMock(SpecificationInterface::class);
        $specificationSecond->expects(
            $this->any()
        )->method(
            'isSatisfiedBy'
        )->with(
            $method
        )->will(
            $this->returnValue($secondSpecificationResult)
        );

        $this->factoryMock->expects(
            $this->at(0)
        )->method(
            'create'
        )->with(
            'SpecificationFirst'
        )->will(
            $this->returnValue($specificationFirst)
        );
        $this->factoryMock->expects(
            $this->at(1)
        )->method(
            'create'
        )->with(
            'SpecificationSecond'
        )->will(
            $this->returnValue($specificationSecond)
        );

        $composite = $this->createComposite(['SpecificationFirst', 'SpecificationSecond']);

        $this->assertEquals(
            $compositeResult,
            $composite->isSatisfiedBy($method),
            'Composite specification is not satisfied by payment method'
        );
    }

    /**
     * @return array
     */
    public function compositeDataProvider()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false]
        ];
    }
}
