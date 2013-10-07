<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an e-mail
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Core\Model;

class NoRouteHandlerListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Core\Model\NoRouteHandlerList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $handlersList = array(
            'default_handler' => array(
                'instance' => 'Magento\Core\Model\Router\NoRouteHandler',
                'sortOrder' => 100
            ),
            'backend_handler' => array(
                'instance'  => 'Magento\Backend\Model\Router\NoRouteHandler',
                'sortOrder' => 10
            ),
        );

        $this->_model = new \Magento\Core\Model\NoRouteHandlerList($this->_objectManagerMock, $handlersList);
    }

    public function testGetHandlers()
    {
        $backendHandlerMock = $this->getMock(
            'Magento\Backend\Model\Router\NoRouteHandler', array(), array(), '', false
        );
        $defaultHandlerMock = $this->getMock('Magento\Core\Model\Router\NoRouteHandler', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with('Magento\Backend\Model\Router\NoRouteHandler')
            ->will($this->returnValue($backendHandlerMock));

        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with('Magento\Core\Model\Router\NoRouteHandler')
            ->will($this->returnValue($defaultHandlerMock));


        $expectedResult = array(
            '0' => $backendHandlerMock,
            '1' => $defaultHandlerMock
        );

        $this->assertEquals($expectedResult, $this->_model->getHandlers());
    }
}
