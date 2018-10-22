<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Test\Unit\Model\ResourceModel\Product;

use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Stock\Status as StockStatus;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as StockStatusResource;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\StockStatusBaseSelectProcessor;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\StockStatusInterface
    as StockStatusConfigurableInterface;

class StockStatusBaseSelectProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StockStatusBaseSelectProcessor
     */
    private $subject;

    /**
     * @var StockConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockConfigMock;

    /**
     * @var StockStatusConfigurableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockStatusConfigurableResourceMock;

    /**
     * @var string
     */
    private $stockStatusTable = 'cataloginventory_stock_status';

    /**
     * @var int
     */
    private $productId = 1;

    /**
     * @var StockStatusResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockStatusResourceMock;

    protected function setUp()
    {
        $this->stockConfigMock = $this->getMockBuilder(StockConfigurationInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->stockStatusResourceMock = $this->getMockBuilder(StockStatusResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockStatusResourceMock->expects($this->any())
            ->method('getMainTable')
            ->willReturn($this->stockStatusTable);

        $this->stockStatusConfigurableResourceMock = $this->getMockBuilder(StockStatusConfigurableInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = (new ObjectManager($this))->getObject(
            StockStatusBaseSelectProcessor::class,
            [
                'stockConfig' => $this->stockConfigMock,
                'stockStatusResource' => $this->stockStatusResourceMock,
                'stockStatusConfigurableResource' => $this->stockStatusConfigurableResourceMock
            ]
        );
    }

    /**
     * @param bool $isShowOutOfStock
     * @param bool $isAllChildOutOfStock
     *
     * @dataProvider processDataProvider
     */
    public function testProcess($isShowOutOfStock, $isAllChildOutOfStock)
    {
        $this->stockConfigMock->expects($this->any())
            ->method('isShowOutOfStock')
            ->willReturn($isShowOutOfStock);

        $this->stockStatusConfigurableResourceMock->expects($this->any())
            ->method('isAllChildOutOfStock')
            ->willReturn($isAllChildOutOfStock);

        /** @var Select|\PHPUnit_Framework_MockObject_MockObject $selectMock */
        $selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($isShowOutOfStock && !$isAllChildOutOfStock) {
            $selectMock->expects($this->once())
                ->method('joinInner')
                ->with(
                    ['stock' => $this->stockStatusTable],
                    sprintf(
                        'stock.product_id = %s.entity_id',
                        BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS
                    ),
                    []
                )
                ->willReturnSelf();
            $selectMock->expects($this->once())
                ->method('where')
                ->with(
                    'stock.stock_status = ?',
                    StockStatus::STATUS_IN_STOCK
                )
                ->willReturnSelf();
        } else {
            $selectMock->expects($this->never())
                ->method($this->anything());
        }

        $this->assertEquals($selectMock, $this->subject->process($selectMock, $this->productId));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            'Out of stock filter was NOT applied [true, true]' => [true, true],
            'Out of stock filter was applied [true, false]' => [true, false],
            'Out of stock filter was NOT applied [false, true]' => [false, true],
            'Out of stock filter was NOT applied [false, false]' => [false, false]
        ];
    }
}
