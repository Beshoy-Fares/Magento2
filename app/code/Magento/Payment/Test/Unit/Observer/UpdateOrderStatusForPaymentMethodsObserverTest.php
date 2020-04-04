<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;

class UpdateOrderStatusForPaymentMethodsObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Payment\Observer\updateOrderStatusForPaymentMethodsObserver
     */
    private $updateOrderStatusForPaymentMethodsObserver;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var \Magento\Sales\Model\Order\Config|MockObject
     */
    private $orderConfigMock;

    /**
     * @var \Magento\Payment\Model\Config|MockObject
     */
    private $paymentConfigMock;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config|MockObject
     */
    private $coreResourceConfigMock;

    /**
     * @var \Magento\Framework\Event\Observer|MockObject
     */
    private $observerMock;

    /**
     * @var \Magento\Framework\Event|MockObject
     */
    private $eventMock;

    const ORDER_STATUS = 'status';

    const METHOD_CODE = 'method_code';

    protected function setUp()
    {
        $this->orderConfigMock = $this->createMock(\Magento\Sales\Model\Order\Config::class);
        $this->paymentConfigMock = $this->createMock(\Magento\Payment\Model\Config::class);
        $this->coreResourceConfigMock = $this->createMock(\Magento\Config\Model\ResourceModel\Config::class);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->updateOrderStatusForPaymentMethodsObserver = $this->objectManagerHelper->getObject(
            \Magento\Payment\Observer\UpdateOrderStatusForPaymentMethodsObserver::class,
            [
                'salesOrderConfig' => $this->orderConfigMock,
                'paymentConfig' => $this->paymentConfigMock,
                'resourceConfig' => $this->coreResourceConfigMock
            ]
        );

        $this->observerMock = $this->getMockBuilder(
            \Magento\Framework\Event\Observer::class
        )->disableOriginalConstructor()->setMethods([])->getMock();
    }

    public function testUpdateOrderStatusForPaymentMethodsNotNewState()
    {
        $this->_prepareEventMockWithMethods(['getState']);
        $this->eventMock->expects($this->once())->method('getState')->will($this->returnValue('NotNewState'));
        $this->updateOrderStatusForPaymentMethodsObserver->execute($this->observerMock);
    }

    public function testUpdateOrderStatusForPaymentMethodsNewState()
    {
        $this->_prepareEventMockWithMethods(['getState', 'getStatus']);
        $this->eventMock->expects($this->once())->method('getState')->will(
            $this->returnValue(\Magento\Sales\Model\Order::STATE_NEW)
        );
        $this->eventMock->expects($this->once())->method('getStatus')->will(
            $this->returnValue(self::ORDER_STATUS)
        );

        $defaultStatus = 'defaultStatus';
        $this->orderConfigMock->expects($this->once())->method('getStateDefaultStatus')->with(
            \Magento\Sales\Model\Order::STATE_NEW
        )->will($this->returnValue($defaultStatus));

        $this->paymentConfigMock->expects($this->once())->method('getActiveMethods')->will(
            $this->returnValue($this->_getPreparedActiveMethods())
        );

        $this->coreResourceConfigMock->expects($this->once())->method('saveConfig')->with(
            'payment/' . self::METHOD_CODE . '/order_status',
            $defaultStatus,
            'default',
            0
        );
        $this->updateOrderStatusForPaymentMethodsObserver->execute($this->observerMock);
    }

    /**
     * Prepares EventMock with set of methods
     *
     * @param $methodsList
     */
    private function _prepareEventMockWithMethods($methodsList)
    {
        $this->eventMock = $this->getMockBuilder(
            \Magento\Framework\Event::class
        )->disableOriginalConstructor()->setMethods($methodsList)->getMock();
        $this->observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->eventMock));
    }

    /**
     * Return mocked data of getActiveMethods
     *
     * @return array
     */
    private function _getPreparedActiveMethods()
    {
        $method1 = $this->getMockBuilder(
            \Magento\Payment\Model\MethodInterface::class
        )->getMockForAbstractClass();
        $method1->expects($this->once())->method('getConfigData')->with('order_status')->will(
            $this->returnValue(self::ORDER_STATUS)
        );
        $method1->expects($this->once())->method('getCode')->will(
            $this->returnValue(self::METHOD_CODE)
        );

        $method2 = $this->getMockBuilder(
            \Magento\Payment\Model\MethodInterface::class
        )->getMockForAbstractClass();
        $method2->expects($this->once())->method('getConfigData')->with('order_status')->will(
            $this->returnValue('not_a_status')
        );

        return [$method1, $method2];
    }
}
