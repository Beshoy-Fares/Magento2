<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

namespace Magento\Swatches\Test\Unit\Model;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Model\SwatchAttributeType;

<<<<<<< HEAD
class SwatchAttributeTypeTest extends \PHPUnit\Framework\TestCase
{

=======
/**
 * Tests for \Magento\Swatches\Model\SwatchAttributeType class.
 */
class SwatchAttributeTypeTest extends \PHPUnit\Framework\TestCase
{
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    /**
     * @var SwatchAttributeType
     */
    private $swatchType;

<<<<<<< HEAD
=======
    /**
     * @inheritdoc
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    protected function setUp()
    {
        parent::setUp();
        $this->swatchType = new SwatchAttributeType(new Json());
    }

    /**
     * @dataProvider provideIsSwatchAttributeTestData
     * @param string $dataValue
     * @param bool $expected
<<<<<<< HEAD
     */
    public function testIsSwatchAttribute($dataValue, $expected)
=======
     * @return void
     */
    public function testIsSwatchAttribute(string $dataValue, bool $expected) : void
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        $this->assertEquals(
            $expected,
            $this->swatchType->isSwatchAttribute(
                $this->createAttributeMock($dataValue)
            )
        );
    }

    /**
<<<<<<< HEAD
     * DataProvider for testIsSwatchAttribute
     * @return array
     */
    public function provideIsSwatchAttributeTestData()
=======
     * DataProvider for testIsSwatchAttribute.
     *
     * @return array
     */
    public function provideIsSwatchAttributeTestData() : array
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        return [
            [Swatch::SWATCH_INPUT_TYPE_TEXT, true],
            [Swatch::SWATCH_INPUT_TYPE_VISUAL, true],
            ['fake', false],
        ];
    }

    /**
     * @dataProvider provideIsTextSwatchAttributeTestData
     * @param string $dataValue
     * @param bool $expected
<<<<<<< HEAD
     */
    public function testIsTextSwatch($dataValue, $expected)
=======
     * @return void
     */
    public function testIsTextSwatch(string $dataValue, bool $expected) : void
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        $this->assertEquals(
            $expected,
            $this->swatchType->isTextSwatch(
                $this->createAttributeMock($dataValue)
            )
        );
    }

    /**
<<<<<<< HEAD
     * DataProvider for testIsTextSwatch
     * @return array
     */
    public function provideIsTextSwatchAttributeTestData()
=======
     * DataProvider for testIsTextSwatch.
     *
     * @return array
     */
    public function provideIsTextSwatchAttributeTestData() : array
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        return [
            [Swatch::SWATCH_INPUT_TYPE_TEXT, true],
            [Swatch::SWATCH_INPUT_TYPE_VISUAL, false],
            ['fake', false],
        ];
    }

    /**
     * @dataProvider provideIsVisualSwatchAttributeTestData
     * @param string $dataValue
     * @param bool $expected
<<<<<<< HEAD
     */
    public function testIsVisualSwatch($dataValue, $expected)
=======
     * @return void
     */
    public function testIsVisualSwatch(string $dataValue, bool $expected) : void
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        $this->assertEquals(
            $expected,
            $this->swatchType->isVisualSwatch(
                $this->createAttributeMock($dataValue)
            )
        );
    }

    /**
<<<<<<< HEAD
     * DataProvider for testIsTextSwatch
     * @return array
     */
    public function provideIsVisualSwatchAttributeTestData()
=======
     * DataProvider for testIsTextSwatch.
     *
     * @return array
     */
    public function provideIsVisualSwatchAttributeTestData() : array
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        return [
            [Swatch::SWATCH_INPUT_TYPE_VISUAL, true],
            [Swatch::SWATCH_INPUT_TYPE_TEXT, false],
            ['fake', false],
        ];
    }

<<<<<<< HEAD
    public function testIfAttributeHasNotAdditionData()
=======
    /**
     * @return void
     */
    public function testIfAttributeHasNotAdditionData() : void
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        /** @var Json $json */
        $json = new Json();
        $encodedAdditionData = $json->serialize([Swatch::SWATCH_INPUT_TYPE_KEY => Swatch::SWATCH_INPUT_TYPE_TEXT]);

        /** @var AttributeInterface | \PHPUnit_Framework_MockObject_MockObject $attributeMock */
        $attributeMock = $this->getMockBuilder(AttributeInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasData', 'getData', 'setData'])
            ->getMockForAbstractClass();

        $attributeMock->expects($this->any())->method('hasData')->willReturn(false);
<<<<<<< HEAD
        $attributeMock->expects($this->at(0))->method('getData')->willReturn('test');
        $attributeMock->expects($this->at(1))->method('getData')->willReturn($encodedAdditionData);
        $attributeMock->expects($this->at(2))->method('getData')->willReturn(Swatch::SWATCH_INPUT_TYPE_TEXT);
        $attributeMock->expects($this->at(3))->method('getData')->willReturn(Swatch::SWATCH_INPUT_TYPE_TEXT);
=======

        $attributeMock->expects($this->any())
            ->method('getData')
            ->willReturnMap(
                [
                    ['additional_data', $encodedAdditionData],
                    [Swatch::SWATCH_INPUT_TYPE_KEY, Swatch::SWATCH_INPUT_TYPE_TEXT],
                ]
            );
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        $this->assertEquals(true, $this->swatchType->isTextSwatch($attributeMock));
        $this->assertEquals(false, $this->swatchType->isVisualSwatch($attributeMock));
    }

    /**
     * @param mixed $getDataReturns
     * @param bool $hasDataReturns
     * @return AttributeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
<<<<<<< HEAD
    protected function createAttributeMock($getDataReturns, $hasDataReturns = true)
=======
    protected function createAttributeMock($getDataReturns, bool $hasDataReturns = true)
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        $attributeMock = $this->getMockBuilder(AttributeInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasData', 'getData', 'setData'])
            ->getMockForAbstractClass();

        $attributeMock->expects($this->any())->method('hasData')->willReturn($hasDataReturns);
        $attributeMock->expects($this->any())->method('getData')->willReturn($getDataReturns);
<<<<<<< HEAD
=======

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        return $attributeMock;
    }
}
