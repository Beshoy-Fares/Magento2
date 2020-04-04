<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model\Checks;

use Magento\Payment\Model\Checks\Composite;
use Magento\Payment\Model\Checks\CompositeFactory;
use Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Payment\Model\Checks\SpecificationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SpecificationFactoryTest extends TestCase
{
    /**
     * Specification key
     */
    const SPECIFICATION_KEY = 'specification';

    /**
     * @var CompositeFactory|MockObject
     */
    private $compositeFactoryMock;

    protected function setUp()
    {
        $this->compositeFactoryMock = $this->getMockBuilder(CompositeFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
    }

    public function testCreate()
    {
        $specification = $this->getMockBuilder(SpecificationInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $specificationMapping = [self::SPECIFICATION_KEY => $specification];

        $expectedComposite = $this->getMockBuilder(Composite::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $modelFactory = new SpecificationFactory($this->compositeFactoryMock, $specificationMapping);
        $this->compositeFactoryMock
            ->expects($this->once())->method('create')
            ->with(['list' => $specificationMapping])
            ->will($this->returnValue($expectedComposite));

        $this->assertEquals($expectedComposite, $modelFactory->create([self::SPECIFICATION_KEY]));
    }
}
