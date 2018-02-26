<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Test\Unit\Controller;

use Magento\Framework\App\Action\Redirect;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Store\Model\Store;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\UrlRewrite\Controller\Router */
    protected $router;

    /** @var \Magento\Framework\App\ActionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $actionFactory;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $url;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\UrlRewrite\Model\UrlFinderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlFinder;

    protected function setUp()
    {
        $this->actionFactory = $this->getMock('Magento\Framework\App\ActionFactory', [], [], '', false);
        $this->url = $this->getMock('Magento\Framework\UrlInterface');
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->response = $this->getMock('Magento\Framework\App\ResponseInterface', ['setRedirect', 'sendResponse']);
        $this->request = $this->getMockBuilder('\Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->urlFinder = $this->getMock('Magento\UrlRewrite\Model\UrlFinderInterface');
        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();

        $this->router = (new ObjectManager($this))->getObject(
            'Magento\UrlRewrite\Controller\Router',
            [
                'actionFactory' => $this->actionFactory,
                'url' => $this->url,
                'storeManager' => $this->storeManager,
                'response' => $this->response,
                'urlFinder' => $this->urlFinder
            ]
        );
    }

    public function testNoRewriteExist()
    {
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue(null));
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));

        $this->assertNull($this->router->match($this->request));
    }

    public function testRewriteAfterStoreSwitcher()
    {
<<<<<<< HEAD
        $this->request->expects($this->any())->method('getPathInfo')->will($this->returnValue('request-path'));
        $this->request->expects($this->any())->method('getParam')->with('___from_store')
            ->will($this->returnValue('old-store'));
        $oldStore = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValueMap([['old-store', $oldStore], [null, $this->store]]));
        $oldStore->expects($this->any())->method('getId')->will($this->returnValue('old-store-id'));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));
        $oldUrlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $oldUrlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('entity-type'));
        $oldUrlRewrite->expects($this->any())->method('getEntityId')->will($this->returnValue('entity-id'));
        $oldUrlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('old-request-path'));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('new-request-path'));
=======
        $initialRequestPath = 'request-path';
        $newRequestPath = 'new-request-path';
        $oldStoreAlias = 'old-store';
        $oldStoreId = 'old-store-id';
        $currentStoreId = 'current-store-id';
        $rewriteEntityType = 'entity-type';
        $rewriteEntityId = 42;
        $redirectUrl = '/' . $newRequestPath;
>>>>>>> e71416c2343... Merge branch 'MAGETWO-70726' into QwertyPR20171107

        $this->request
            ->expects($this->any())
            ->method('getParam')
            ->with('___from_store')
            ->willReturn($oldStoreAlias);
        $this->request
            ->expects($this->any())
            ->method('getPathInfo')
            ->willReturn($initialRequestPath);

        $oldStore = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $oldStore->expects($this->any())
            ->method('getId')
            ->willReturn($oldStoreId);
        $this->store
            ->expects($this->any())
            ->method('getId')
            ->willReturn($currentStoreId);

        $this->storeManager
            ->expects($this->any())
            ->method('getStore')
            ->willReturnMap([[$oldStoreAlias, $oldStore], [null, $this->store]]);

        $oldUrlRewrite = $this->getMockBuilder(UrlRewrite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $oldUrlRewrite->expects($this->any())
            ->method('getEntityType')
            ->willReturn($rewriteEntityType);
        $oldUrlRewrite->expects($this->any())
            ->method('getEntityId')
            ->willReturn($rewriteEntityId);
        $oldUrlRewrite->expects($this->any())
            ->method('getRedirectType')
            ->willReturn(0);
        $urlRewrite = $this->getMockBuilder(UrlRewrite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlRewrite->expects($this->any())
            ->method('getRequestPath')
            ->willReturn($newRequestPath);

        $this->urlFinder
            ->expects($this->any())
            ->method('findOneByData')
            ->willReturnMap([
                [
                    [
                        UrlRewrite::REQUEST_PATH => $initialRequestPath,
                        UrlRewrite::STORE_ID     => $oldStoreId,
                    ],
                    $oldUrlRewrite,
                ],
                [
                    [
                        UrlRewrite::ENTITY_TYPE   => $rewriteEntityType,
                        UrlRewrite::ENTITY_ID     => $rewriteEntityId,
                        UrlRewrite::STORE_ID      => $currentStoreId,
                        UrlRewrite::REDIRECT_TYPE => 0,
                    ],
                    $urlRewrite,
                ],
<<<<<<< HEAD
            ])
        );
        $this->response->expects($this->once())->method('setRedirect')
            ->with('new-request-path', OptionProvider::TEMPORARY);
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect');
=======
            ]);

        $this->url
            ->expects($this->once())
            ->method('getUrl')
            ->with('', ['_direct' => $newRequestPath])
            ->willReturn($redirectUrl);
        $this->response
            ->expects($this->once())
            ->method('setRedirect')
            ->with($redirectUrl, OptionProvider::TEMPORARY);
        $this->request
            ->expects($this->once())
            ->method('setDispatched')
            ->with(true);
        $this->actionFactory
            ->expects($this->once())
            ->method('create')
            ->with(Redirect::class);
>>>>>>> e71416c2343... Merge branch 'MAGETWO-70726' into QwertyPR20171107

        $this->router->match($this->request);
    }

    public function testNoRewriteAfterStoreSwitcherWhenNoOldRewrite()
    {
        $this->request->expects($this->any())->method('getPathInfo')->will($this->returnValue('request-path'));
        $this->request->expects($this->any())->method('getParam')->with('___from_store')
            ->will($this->returnValue('old-store'));
        $oldStore = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValueMap([['old-store', $oldStore], [null, $this->store]]));
        $oldStore->expects($this->any())->method('getId')->will($this->returnValue('old-store-id'));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));
        $oldUrlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $oldUrlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('entity-type'));
        $oldUrlRewrite->expects($this->any())->method('getEntityId')->will($this->returnValue('entity-id'));
        $oldUrlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('request-path'));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('request-path'));

        $this->assertNull($this->router->match($this->request));
    }

    public function testNoRewriteAfterStoreSwitcherWhenOldRewriteEqualsToNewOne()
    {
        $this->request->expects($this->any())->method('getPathInfo')->will($this->returnValue('request-path'));
        $this->request->expects($this->any())->method('getParam')->with('___from_store')
            ->will($this->returnValue('old-store'));
        $oldStore = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValueMap([['old-store', $oldStore], [null, $this->store]]));
        $oldStore->expects($this->any())->method('getId')->will($this->returnValue('old-store-id'));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));
        $oldUrlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $oldUrlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('entity-type'));
        $oldUrlRewrite->expects($this->any())->method('getEntityId')->will($this->returnValue('entity-id'));
        $oldUrlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('old-request-path'));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('old-request-path'));

        $this->urlFinder->expects($this->any())->method('findOneByData')->will(
            $this->returnValueMap([
                [
                    [UrlRewrite::REQUEST_PATH => 'request-path', UrlRewrite::STORE_ID => 'old-store-id'],
                    $oldUrlRewrite,
                ],
                [
                    [
                        UrlRewrite::ENTITY_TYPE => 'entity-type',
                        UrlRewrite::ENTITY_ID => 'entity-id',
                        UrlRewrite::STORE_ID => 'current-store-id',
                        UrlRewrite::IS_AUTOGENERATED => 1,
                    ],
                    $urlRewrite
                ],
            ])
        );

        $this->assertNull($this->router->match($this->request));
    }

    public function testMatchWithRedirect()
    {
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue('redirect-code'));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue('target-path'));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->response->expects($this->once())->method('setRedirect')
            ->with('new-target-path', 'redirect-code');
        $this->url->expects($this->once())->method('getUrl')->with('', ['_direct' => 'target-path'])
            ->will($this->returnValue('new-target-path'));
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect');

        $this->router->match($this->request);
    }

    public function testMatchWithCustomInternalRedirect()
    {
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('custom'));
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue('redirect-code'));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue('target-path'));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->response->expects($this->once())->method('setRedirect')->with('a', 'redirect-code');
        $this->url->expects($this->once())->method('getUrl')->with('', ['_direct' => 'target-path'])->willReturn('a');
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect');

        $this->router->match($this->request);
    }

    /**
     * @param string $targetPath
     * @dataProvider externalRedirectTargetPathDataProvider
     */
    public function testMatchWithCustomExternalRedirect($targetPath)
    {
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('custom'));
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue('redirect-code'));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue($targetPath));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->response->expects($this->once())->method('setRedirect')->with($targetPath, 'redirect-code');
        $this->url->expects($this->never())->method('getUrl');
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect');

        $this->router->match($this->request);
    }

    /**
     * @return array
     */
    public function externalRedirectTargetPathDataProvider()
    {
        return [
            ['http://example.com'],
            ['https://example.com'],
        ];
    }

    public function testMatch()
    {
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue(0));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue('target-path'));
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('request-path'));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->request->expects($this->once())->method('setPathInfo')->with('/target-path');
        $this->request->expects($this->once())->method('setAlias')
            ->with(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, 'request-path');
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Forward');

        $this->router->match($this->request);
    }
}
