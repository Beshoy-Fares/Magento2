<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Attribute\Source;

class StockStatusTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\Catalog\Model\Product\Attribute\Source\StockStatus */
    protected $stockStatus;

    protected function setUp()
    {
        parent::setUp();

        $this->stockStatus = $this->createMock(
            \Magento\Catalog\Model\Product\Attribute\Source\StockStatus::class
        );
    }

    /**
     * Check option data
     */
    public function testGetOptionArray()
    {
        $this->assertEquals([1 => 'In Stock', 0 => 'Out of Stock'], $this->stastockStatus->getOptionArray());
    }

    /**
     * Check text by option id
     *
     * @dataProvider getOptionTextDataProvider
     * @param string $text
     * @param string $optionId
     */
    public function testGetOptionText($text, $optionId)
    {
        $this->assertEquals($text, $this->stockStatus->getOptionText($optionId));
    }

    /**
     * Array with text data for test
     *
     * @return array
     */
    public function getOptionTextDataProvider()
    {
        return [
            [
                'text' => 'In Stock',
                'id' => '1',
            ],
            [
                'text' => 'Out of Stock',
                'id' => '0'
            ]
        ];
    }
}
