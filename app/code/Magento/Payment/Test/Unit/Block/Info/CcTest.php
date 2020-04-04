<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Block\Info;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info\Cc;
use Magento\Payment\Model\Config;
use Magento\Payment\Model\Info;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CcTest extends TestCase
{
    /**
     * @var Cc
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config|MockObject
     */
    private $paymentConfig;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $localeDateMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->paymentConfig = $this->createMock(Config::class);
        $this->localeDateMock = $this->createMock(TimezoneInterface::class);
        $context = $this->createPartialMock(Context::class, ['getLocaleDate']);
        $context->expects($this->any())
            ->method('getLocaleDate')
            ->will($this->returnValue($this->localeDateMock));
        $this->model = $this->objectManager->getObject(
            Cc::class,
            [
                'paymentConfig' => $this->paymentConfig,
                'context' => $context
            ]
        );
    }

    /**
     * @dataProvider getCcTypeNameDataProvider
     */
    public function testGetCcTypeName($configCcTypes, $ccType, $expected)
    {
        $this->paymentConfig->expects($this->any())
            ->method('getCcTypes')
            ->will($this->returnValue($configCcTypes));
        $paymentInfo = $this->createPartialMock(Info::class, ['getCcType']);
        $paymentInfo->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue($ccType));
        $this->model->setData('info', $paymentInfo);
        $this->assertEquals($expected, $this->model->getCcTypeName());
    }

    /**
     * @return array
     */
    public function getCcTypeNameDataProvider()
    {
        return [
            [['VS', 'MC', 'JCB'], 'JCB', 'JCB'],
            [['VS', 'MC', 'JCB'], 'BNU', 'BNU'],
            [['VS', 'MC', 'JCB'], null, 'N/A'],
        ];
    }

    /**
     * @dataProvider hasCcExpDateDataProvider
     */
    public function testHasCcExpDate($ccExpMonth, $ccExpYear, $expected)
    {
        $paymentInfo = $this->createPartialMock(Info::class, ['getCcExpMonth', 'getCcExpYear']);
        $paymentInfo->expects($this->any())
            ->method('getCcExpMonth')
            ->will($this->returnValue($ccExpMonth));
        $paymentInfo->expects($this->any())
            ->method('getCcExpYear')
            ->will($this->returnValue($ccExpYear));
        $this->model->setData('info', $paymentInfo);
        $this->assertEquals($expected, $this->model->hasCcExpDate());
    }

    /**
     * @return array
     */
    public function hasCcExpDateDataProvider()
    {
        return [
            [0, 1, true],
            [1, 0, true],
            [0, 0, false]
        ];
    }

    /**
     * @dataProvider ccExpMonthDataProvider
     */
    public function testGetCcExpMonth($ccExpMonth, $expected)
    {
        $paymentInfo = $this->createPartialMock(Info::class, ['getCcExpMonth']);
        $paymentInfo->expects($this->any())
            ->method('getCcExpMonth')
            ->will($this->returnValue($ccExpMonth));
        $this->model->setData('info', $paymentInfo);
        $this->assertEquals($expected, $this->model->getCcExpMonth());
    }

    /**
     * @return array
     */
    public function ccExpMonthDataProvider()
    {
        return [
            [2, '02'],
            [12, '12']
        ];
    }

    /**
     * @dataProvider getCcExpDateDataProvider
     */
    public function testGetCcExpDate($ccExpMonth, $ccExpYear)
    {
        $paymentInfo = $this->createPartialMock(Info::class, ['getCcExpMonth', 'getCcExpYear']);
        $paymentInfo
            ->expects($this->any())
            ->method('getCcExpMonth')
            ->willReturn($ccExpMonth);
        $paymentInfo
            ->expects($this->any())
            ->method('getCcExpYear')
            ->willReturn($ccExpYear);
        $this->model->setData('info', $paymentInfo);

        $this->localeDateMock
            ->expects($this->exactly(2))
            ->method('getConfigTimezone')
            ->willReturn('America/Los_Angeles');

        $this->assertEquals($ccExpYear, $this->model->getCcExpDate()->format('Y'));
        $this->assertEquals($ccExpMonth, $this->model->getCcExpDate()->format('m'));
    }

    /**
     * @return array
     */
    public function getCcExpDateDataProvider()
    {
        return [
            [3, 2015],
            [12, 2011],
            [01, 2036]
        ];
    }
}
