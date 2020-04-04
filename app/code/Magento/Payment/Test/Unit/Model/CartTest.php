<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Model\Cart;
use Magento\Payment\Model\Cart\SalesModel\Factory;
use Magento\Payment\Model\Cart\SalesModel\SalesModelInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    /**
     * var Cart
     */
    private $model;

    /**
     * @var ManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var SalesModelInterface|MockObject
     */
    private $salesModelMock;

    protected function setUp()
    {
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $this->salesModelMock = $this->createMock(SalesModelInterface::class);
        $factoryMock = $this->createMock(Factory::class);
        $factoryMock->expects($this->once())->method('create')->will($this->returnValue($this->salesModelMock));

        $this->model = new Cart($factoryMock, $this->eventManagerMock, null);
    }

    /**
     * Test sales model getter
     */
    public function testGetSalesModel()
    {
        $this->assertTrue($this->salesModelMock === $this->model->getSalesModel());
    }

    /**
     * Test addCustomItem()
     */
    public function testAddCustomItem()
    {
        $this->salesModelMock->expects(
            $this->once()
        )->method(
            'getAllItems'
        )->will(
            $this->returnValue($this->_getSalesModelItems())
        );
        $this->model->getAllItems();
        $this->model->addCustomItem('test', 10, 10.5, 'some_id');
        $items = $this->model->getAllItems();
        $customItem = array_pop($items);
        $this->assertTrue(
            $customItem->getName() == 'test' &&
            $customItem->getQty() == 10 &&
            $customItem->getAmount() == 10.5 &&
            $customItem->getId() == 'some_id'
        );
    }

    /**
     * @param array $transferFlags
     * @param array $salesModelItems
     * @param array $salesModelAmounts
     * @param array $expected
     * @dataProvider cartDataProvider
     */
    public function testGetAmounts($transferFlags, $salesModelItems, $salesModelAmounts, $expected)
    {
        $amounts = $this->_collectItemsAndAmounts($transferFlags, $salesModelItems, $salesModelAmounts);
        $this->assertEquals($expected, $amounts);

        // check that method just return calculated result for further calls
        $this->eventManagerMock->expects($this->never())->method('dispatch');
        $amounts = $this->model->getAmounts();
        $this->assertEquals($expected, $amounts);
    }

    /**
     * @param array $transferFlags
     * @param array $salesModelItems
     * @param array $salesModelAmounts
     * @dataProvider cartDataProvider
     */
    public function testGetAllItems($transferFlags, $salesModelItems, $salesModelAmounts)
    {
        $this->_collectItemsAndAmounts($transferFlags, $salesModelItems, $salesModelAmounts);

        $customItems = [];
        if ($transferFlags['transfer_shipping']) {
            $customItems[] = new DataObject(
                ['name' => 'Shipping', 'qty' => 1, 'amount' => $salesModelAmounts['BaseShippingAmount']]
            );
        }
        if ($transferFlags['transfer_discount']) {
            $customItems[] = new DataObject(
                ['name' => 'Discount', 'qty' => 1, 'amount' => -1.00 * $salesModelAmounts['BaseDiscountAmount']]
            );
        }

        $cartItems = $this->_convertToCartItems($salesModelItems);
        $expected = array_merge($cartItems, $customItems);
        $areEqual = $this->_compareSalesItems($expected, $this->model->getAllItems());
        $this->assertTrue($areEqual);
    }

    /**
     * Test all amount specific methods i.e. add...(), set...(), get...()
     */
    public function testAmountSettersAndGetters()
    {
        foreach (['Discount', 'Shipping', 'Tax'] as $amountType) {
            $setMethod = 'set' . $amountType;
            $getMethod = 'get' . $amountType;
            $addMethod = 'add' . $amountType;

            $this->model->{$setMethod}(10);
            $this->assertEquals(10, $this->model->{$getMethod}());

            $this->model->{$addMethod}(5);
            $this->assertEquals(15, $this->model->{$getMethod}());

            $this->model->{$addMethod}(-20);
            $this->assertEquals(-5, $this->model->{$getMethod}());

            $this->model->{$setMethod}(10);
            $this->assertEquals(10, $this->model->{$getMethod}());
        }

        // there is no method setSubtotal(), so test the following separately
        $this->model->addSubtotal(10);
        $this->assertEquals(10, $this->model->getSubtotal());

        $this->model->addSubtotal(2);
        $this->assertEquals(12, $this->model->getSubtotal());

        $this->model->addSubtotal(-20);
        $this->assertEquals(-8, $this->model->getSubtotal());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function cartDataProvider()
    {
        return [
            // 1. All transfer flags set to true
            [
                ['transfer_shipping' => true, 'transfer_discount' => true],
                $this->_getSalesModelItems(),
                [
                    'BaseDiscountAmount' => 15.0,
                    'BaseShippingAmount' => 20.0,
                    'BaseSubtotal' => 100.0,
                    'BaseTaxAmount' => 8.0
                ],
                [
                    Cart::AMOUNT_DISCOUNT => 0.0,
                    Cart::AMOUNT_SHIPPING => 0.0,
                    Cart::AMOUNT_SUBTOTAL => 105.0, // = 100.5 + shipping - discount
                    Cart::AMOUNT_TAX => 8.0
                ]
            ],
            // 2. All transfer flags set to false
            [
                ['transfer_shipping' => false, 'transfer_discount' => false],
                $this->_getSalesModelItems(),
                [
                    'BaseDiscountAmount' => 15.0,
                    'BaseShippingAmount' => 20.0,
                    'BaseSubtotal' => 100.0,
                    'BaseTaxAmount' => 8.0
                ],
                [
                    Cart::AMOUNT_DISCOUNT => 15.0,
                    Cart::AMOUNT_SHIPPING => 20.0,
                    Cart::AMOUNT_SUBTOTAL => 100.0,
                    Cart::AMOUNT_TAX => 8.0
                ]
            ],
            // 3. Shipping transfer flag set to true, discount to false, sales items are empty (don't affect result)
            [
                ['transfer_shipping' => true, 'transfer_discount' => false],
                [],
                [
                    'BaseDiscountAmount' => 15.0,
                    'BaseShippingAmount' => 20.0,
                    'BaseSubtotal' => 100.0,
                    'BaseTaxAmount' => 8.0
                ],
                [
                    Cart::AMOUNT_DISCOUNT => 15.0,
                    Cart::AMOUNT_SHIPPING => 0.0,
                    Cart::AMOUNT_SUBTOTAL => 120.0,
                    Cart::AMOUNT_TAX => 8.0
                ]
            ]
        ];
    }

    /**
     * Return true if arrays of cart sales items are equal, false otherwise. Elements order not considered
     *
     * @param array $salesItemsA
     * @param array $salesItemsB
     * @return bool
     */
    protected function _compareSalesItems(array $salesItemsA, array $salesItemsB)
    {
        if (count($salesItemsA) != count($salesItemsB)) {
            return false;
        }

        $toStringCallback = function (&$item) {
            $item = $item->toString();
        };

        array_walk($salesItemsA, $toStringCallback);
        array_walk($salesItemsB, $toStringCallback);

        sort($salesItemsA);
        sort($salesItemsB);

        return implode('', $salesItemsA) == implode('', $salesItemsB);
    }

    /**
     * Collect sales model items and calculate amounts of sales model
     *
     * @param array $transferFlags
     * @param array $salesModelItems
     * @param array $salesModelAmounts
     * @return array Cart amounts
     */
    protected function _collectItemsAndAmounts($transferFlags, $salesModelItems, $salesModelAmounts)
    {
        if ($transferFlags['transfer_shipping']) {
            $this->model->setTransferShippingAsItem();
        }
        if ($transferFlags['transfer_discount']) {
            $this->model->setTransferDiscountAsItem();
        }

        $this->eventManagerMock->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('payment_cart_collect_items_and_amounts'),
            $this->equalTo(['cart' => $this->model])
        );

        $this->salesModelMock->expects(
            $this->once()
        )->method(
            'getAllItems'
        )->will(
            $this->returnValue($salesModelItems)
        );

        foreach ($salesModelAmounts as $key => $value) {
            $this->salesModelMock->expects($this->once())->method('get' . $key)->will($this->returnValue($value));
        }

        return $this->model->getAmounts();
    }

    /**
     * Return sales model items
     *
     * @return array
     */
    protected function _getSalesModelItems()
    {
        $product = new DataObject(['id' => '1']);
        return [
            new DataObject(
                ['name' => 'name 1', 'qty' => 1, 'price' => 0.1, 'original_item' => $product]
            ),
            new DataObject(
                ['name' => 'name 2', 'qty' => 2, 'price' => 1.2, 'original_item' => $product]
            ),
            new DataObject(
                [
                    'parent_item' => 'parent item 3',
                    'name' => 'name 3',
                    'qty' => 3,
                    'price' => 2.3,
                    'original_item' => $product,
                ]
            )
        ];
    }

    /**
     * Convert sales model items to cart items
     *
     * @param array $salesModelItems
     * @return array
     */
    protected function _convertToCartItems(array $salesModelItems)
    {
        $result = [];
        foreach ($salesModelItems as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $result[] = new DataObject(
                [
                    'name' => $item->getName(),
                    'qty' => $item->getQty(),
                    'amount' => $item->getPrice(),
                    'id' => $item->getOriginalItem()->getId(),
                ]
            );
        }
        return $result;
    }
}
