<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\Component\Product;

use Magento\Catalog\Ui\Component\Product\MassAction;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

<<<<<<< HEAD
/**
 * Test for Magento\Catalog\Ui\Component\Product\MassAction class.
 */
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
class MassActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationMock;

    /**
     * @var MassAction
     */
    private $massAction;

<<<<<<< HEAD
    /**
     * @inheritdoc
     */
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockBuilder(ContextInterface::class)
            ->getMockForAbstractClass();
        $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
            ->getMockForAbstractClass();

        $this->massAction = $this->objectManager->getObject(
            MassAction::class,
            [
                'authorization' => $this->authorizationMock,
                'context' => $this->contextMock,
<<<<<<< HEAD
                'data' => [],
=======
                'data' => []
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ]
        );
    }

<<<<<<< HEAD
    /**
     * @return void
     */
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    public function testGetComponentName()
    {
        $this->assertTrue($this->massAction->getComponentName() === MassAction::NAME);
    }

    /**
     * @param string $componentName
     * @param array $componentData
     * @param bool $isAllowed
     * @param bool $expectActionConfig
     * @return void
     * @dataProvider getPrepareDataProvider
     */
<<<<<<< HEAD
    public function testPrepare(
        string $componentName,
        array $componentData,
        bool $isAllowed = true,
        bool $expectActionConfig = true
    ) {
=======
    public function testPrepare($componentName, $componentData, $isAllowed = true, $expectActionConfig = true)
    {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        $processor = $this->getMockBuilder(\Magento\Framework\View\Element\UiComponent\Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->atLeastOnce())->method('getProcessor')->willReturn($processor);
        /** @var \Magento\Ui\Component\MassAction $action */
        $action = $this->objectManager->getObject(
            \Magento\Ui\Component\MassAction::class,
            [
                'context' => $this->contextMock,
                'data' => [
                    'name' => $componentName,
                    'config' => $componentData,
                ]
            ]
        );
        $this->authorizationMock->method('isAllowed')
            ->willReturn($isAllowed);
        $this->massAction->addComponent('action', $action);
        $this->massAction->prepare();
        $expected = $expectActionConfig ? ['actions' => [$action->getConfiguration()]] : [];
        $this->assertEquals($expected, $this->massAction->getConfiguration());
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getPrepareDataProvider() : array
    {
        return [
            [
                'test_component1',
                [
                    'type' => 'first_action',
                    'label' => 'First Action',
<<<<<<< HEAD
                    'url' => '/module/controller/firstAction',
=======
                    'url' => '/module/controller/firstAction'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ],
            ],
            [
                'test_component2',
                [
                    'type' => 'second_action',
                    'label' => 'Second Action',
                    'actions' => [
                        [
                            'type' => 'second_sub_action1',
                            'label' => 'Second Sub Action 1',
<<<<<<< HEAD
                            'url' => '/module/controller/secondSubAction1',
=======
                            'url' => '/module/controller/secondSubAction1'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                        ],
                        [
                            'type' => 'second_sub_action2',
                            'label' => 'Second Sub Action 2',
<<<<<<< HEAD
                            'url' => '/module/controller/secondSubAction2',
                        ],
                    ],
=======
                            'url' => '/module/controller/secondSubAction2'
                        ],
                    ]
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ],
            ],
            [
                'status_component',
                [
                    'type' => 'status',
                    'label' => 'Status',
                    'actions' => [
                        [
                            'type' => 'enable',
                            'label' => 'Second Sub Action 1',
<<<<<<< HEAD
                            'url' => '/module/controller/enable',
=======
                            'url' => '/module/controller/enable'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                        ],
                        [
                            'type' => 'disable',
                            'label' => 'Second Sub Action 2',
<<<<<<< HEAD
                            'url' => '/module/controller/disable',
                        ],
                    ],
=======
                            'url' => '/module/controller/disable'
                        ],
                    ]
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ],
            ],
            [
                'status_component_not_allowed',
                [
                    'type' => 'status',
                    'label' => 'Status',
                    'actions' => [
                        [
                            'type' => 'enable',
                            'label' => 'Second Sub Action 1',
<<<<<<< HEAD
                            'url' => '/module/controller/enable',
=======
                            'url' => '/module/controller/enable'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                        ],
                        [
                            'type' => 'disable',
                            'label' => 'Second Sub Action 2',
<<<<<<< HEAD
                            'url' => '/module/controller/disable',
                        ],
                    ],
                ],
                false,
                false,
=======
                            'url' => '/module/controller/disable'
                        ],
                    ]
                ],
                false,
                false
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ],
            [
                'delete_component',
                [
                    'type' => 'delete',
                    'label' => 'First Action',
<<<<<<< HEAD
                    'url' => '/module/controller/delete',
=======
                    'url' => '/module/controller/delete'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ],
            ],
            [
                'delete_component_not_allowed',
                [
                    'type' => 'delete',
                    'label' => 'First Action',
<<<<<<< HEAD
                    'url' => '/module/controller/delete',
                ],
                false,
                false,
=======
                    'url' => '/module/controller/delete'
                ],
                false,
                false
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ],
            [
                'attributes_component',
                [
                    'type' => 'delete',
                    'label' => 'First Action',
<<<<<<< HEAD
                    'url' => '/module/controller/attributes',
=======
                    'url' => '/module/controller/attributes'
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ],
            ],
            [
                'attributes_component_not_allowed',
                [
                    'type' => 'delete',
                    'label' => 'First Action',
<<<<<<< HEAD
                    'url' => '/module/controller/attributes',
                ],
                false,
                false,
=======
                    'url' => '/module/controller/attributes'
                ],
                false,
                false
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param string $actionType
     * @param int $callNum
     * @param string $resource
     * @param bool $isAllowed
<<<<<<< HEAD
     * @return void
     * @dataProvider isActionAllowedDataProvider
     */
    public function testIsActionAllowed(
        bool $expected,
        string $actionType,
        int $callNum,
        string $resource = '',
        bool $isAllowed = true
    ) {
=======
     * @dataProvider isActionAllowedDataProvider
     */
    public function testIsActionAllowed($expected, $actionType, $callNum, $resource = '', $isAllowed = true)
    {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        $this->authorizationMock->expects($this->exactly($callNum))
            ->method('isAllowed')
            ->with($resource)
            ->willReturn($isAllowed);

        $this->assertEquals($expected, $this->massAction->isActionAllowed($actionType));
    }

    /**
     * @return array
     */
<<<<<<< HEAD
    public function isActionAllowedDataProvider(): array
=======
    public function isActionAllowedDataProvider()
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        return [
            'other' => [true, 'other', 0,],
            'delete-allowed' => [true, 'delete', 1, 'Magento_Catalog::products'],
            'delete-not-allowed' => [false, 'delete', 1, 'Magento_Catalog::products', false],
            'status-allowed' => [true, 'status', 1, 'Magento_Catalog::products'],
            'status-not-allowed' => [false, 'status', 1, 'Magento_Catalog::products', false],
            'attributes-allowed' => [true, 'attributes', 1, 'Magento_Catalog::update_attributes'],
            'attributes-not-allowed' => [false, 'attributes', 1, 'Magento_Catalog::update_attributes', false],
<<<<<<< HEAD
=======

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        ];
    }
}
