<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model\ResourceModel\Grid;

use Magento\Payment\Helper\Data;
use Magento\Payment\Model\ResourceModel\Grid\TypeList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TypeListTest extends TestCase
{
    /**
     * @var TypeList
     */
    private $typesArrayModel;

    /**
     * @var Data|MockObject
     */
    private $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->createMock(Data::class);
        $this->typesArrayModel = new TypeList($this->helperMock);
    }

    public function testToOptionArray()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('getPaymentMethodList')
            ->with(true)
            ->will($this->returnValue(['group data']));
        $this->assertEquals(['group data'], $this->typesArrayModel->toOptionArray());
    }
}
