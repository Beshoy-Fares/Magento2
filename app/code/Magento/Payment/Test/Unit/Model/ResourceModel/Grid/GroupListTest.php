<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model\ResourceModel\Grid;

use Magento\Payment\Helper\Data;
use Magento\Payment\Model\ResourceModel\Grid\GroupList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GroupListTest extends TestCase
{
    /**
     * @var GroupList
     */
    private $groupArrayModel;

    /**
     * @var MockObject
     */
    private $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->createMock(Data::class);
        $this->groupArrayModel = new GroupList($this->helperMock);
    }

    public function testToOptionArray()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('getPaymentMethodList')
            ->with(true, true, true)
            ->will($this->returnValue(['group data']));
        $this->assertEquals(['group data'], $this->groupArrayModel->toOptionArray());
    }
}
