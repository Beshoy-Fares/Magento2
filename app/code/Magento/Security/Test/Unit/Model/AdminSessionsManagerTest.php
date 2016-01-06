<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test class for \Magento\Security\Model\AdminSessionsManager testing
 */
class AdminSessionsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Magento\Security\Model\AdminSessionsManager
     */
    protected $model;

    /**
     * @var \Magento\Security\Model\AdminSessionInfoFactory
     */
    protected $adminSessionInfoFactory;

    /**
     * @var \Magento\Security\Model\AdminSessionInfo
     */
    protected $currentSession;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Security\Model\ResourceModel\AdminSessionInfo\Collection
     */
    protected $collectionMock;

    /**
     * @var \Magento\Security\Helper\SecurityConfig
     */
    protected $securityConfig;

    /**
     * @var \Magento\User\Model\User
     */
    protected $userMock;

    /**
     * @var \Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory
     */
    protected $adminSessionInfoCollectionFactory;

    /**
     * @var  \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Init mocks for tests
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->adminSessionInfoFactory =  $this->getMock(
            '\Magento\Security\Model\AdminSessionInfoFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->adminSessionInfoCollectionFactory =  $this->getMock(
            '\Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->currentSession =  $this->getMock(
            '\Magento\Security\Model\AdminSessionInfo',
            ['isActive', 'getStatus', 'load'],
            [],
            '',
            false
        );

        $this->authSession =  $this->getMock(
            '\Magento\Backend\Model\Auth\Session',
            ['isActive', 'getStatus', 'getUser', 'getId', 'getSessionId'],
            [],
            '',
            false
        );

        $this->collectionMock =  $this->getMock(
            '\Magento\Security\Model\ResourceModel\AdminSessionInfo\Collection',
            ['filterByUser', 'filterExpiredSessions', 'loadData', 'setDataToAll', 'save'],
            [],
            '',
            false
        );

        $this->securityConfig =  $this->getMock(
            '\Magento\Security\Helper\SecurityConfig',
            ['getAdminSessionLifetime'],
            [],
            '',
            false
        );

        $this->userMock =  $this->getMock(
            '\Magento\User\Model\User',
            ['getId'],
            [],
            '',
            false
        );

        $this->model = $this->objectManager->getObject(
            '\Magento\Security\Model\AdminSessionsManager',
            [
                'authSession' => $this->authSession,
                'adminSessionInfoFactory' => $this->adminSessionInfoFactory,
                'adminSessionInfoCollectionFactory' => $this->adminSessionInfoCollectionFactory,
                'securityConfig' => $this->securityConfig
            ]
        );
    }

    /**
     * @param string $expectedResult
     * @param bool $isActiveSession
     * @param int $sessionStatus
     * @dataProvider dataProviderLogoutReasonMessage
     */
    public function testGetLogoutReasonMessage($expectedResult, $isActiveSession, $sessionStatus)
    {
        $this->adminSessionInfoFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->currentSession);
        $this->currentSession->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue($isActiveSession));
        $this->currentSession->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($sessionStatus));

        $this->assertEquals($expectedResult, $this->model->getLogoutReasonMessage());
    }

    /**
     * @return array
     */
    public function dataProviderLogoutReasonMessage()
    {
        return [
            [
                'expectedResult' => 'Someone logged into this account from another device or browser.'
                    . ' Your current session is terminated.',
                'isActiveSession' => false,
                'sessionStatus' => \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_BY_LOGIN
            ],
            [
                'expectedResult' => 'Your current session is terminated by another user of this account.',
                'isActiveSession' => false,
                'sessionStatus' => \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_MANUALLY
            ],
            [
                'expectedResult' => 'Your current session has been expired.',
                'isActiveSession' => false,
                'sessionStatus' => \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT
            ],
            [
                'expectedResult' => '',
                'isActiveSession' => true,
                'sessionStatus' => ''
            ],
        ];
    }

    /**
     * @return void
     */
    public function testGetSessionsForCurrentUser()
    {
        $useId = 1;
        $sessionLifetime = 100;
        $this->adminSessionInfoCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->authSession->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userMock);
        $this->userMock->expects($this->once())
            ->method('getId')
            ->willReturn($useId);
        $this->collectionMock->expects($this->once())->method('filterByUser')
            ->with($useId, \Magento\Security\Model\AdminSessionInfo::LOGGED_IN)
            ->willReturnSelf();
        $this->securityConfig->expects($this->once())
            ->method('getAdminSessionLifetime')
            ->willReturn($sessionLifetime);
        $this->collectionMock->expects($this->once())
            ->method('filterExpiredSessions')
            ->with($sessionLifetime)
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())
            ->method('loadData')
            ->willReturnSelf();

        $this->assertSame($this->collectionMock, $this->model->getSessionsForCurrentUser());
    }

    /**
     * @return void
     */
    public function testLogoutAnotherUserSessions()
    {
        $useId = 1;
        $sessionLifetime = 100;
        $sessionId = 50;
        $this->adminSessionInfoCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->authSession->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userMock);
        $this->authSession->expects($this->once())
            ->method('getSessionId')
            ->willReturn($sessionId);
        $this->userMock->expects($this->once())
            ->method('getId')
            ->willReturn($useId);
        $this->collectionMock->expects($this->once())
            ->method('filterByUser')
            ->with($useId, \Magento\Security\Model\AdminSessionInfo::LOGGED_IN, $sessionId)
            ->willReturnSelf();
        $this->securityConfig->expects($this->once())
            ->method('getAdminSessionLifetime')
            ->willReturn($sessionLifetime);
        $this->collectionMock->expects($this->once())
            ->method('filterExpiredSessions')
            ->with($sessionLifetime)
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())
            ->method('loadData')
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())
            ->method('setDataToAll')
            ->with($this->equalTo('status'), \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_MANUALLY)
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())
            ->method('save');

        $this->model->logoutAnotherUserSessions();
    }
}
