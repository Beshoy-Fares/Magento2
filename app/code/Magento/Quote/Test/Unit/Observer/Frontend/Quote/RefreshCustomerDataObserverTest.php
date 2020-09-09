<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Observer\Frontend\Quote;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Quote\Observer\Frontend\Quote\RefreshCustomerDataObserver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;

class RefreshCustomerDataObserverTest extends TestCase
{
    /**
     * @var RefreshCustomerDataObserver
     */
    private $observer;

    /**
     * @var PhpCookieManager|MockObject
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory|MockObject
     */
    private $metadataFactory;

    /**
     * @var CookieMetadata|MockObject
     */
    private $metadata;

    /**
     * @var Session|MockObject
     */
    private $checkoutSession;

    /**
     * @var MockObject
     */
    private $quoteMock;

    /**
     * @var QuoteResource|MockObject
     */
    private $quoteResource;

    protected function setUp(): void
    {
        $this->quoteMock = $this->createMock(Quote::class);
        $this->cookieManager = $this->getMockBuilder(PhpCookieManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataFactory = $this->getMockBuilder(CookieMetadataFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata = $this->getMockBuilder(CookieMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quoteResource = $this->getMockBuilder(QuoteResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new RefreshCustomerDataObserver(
            $this->checkoutSession,
            $this->cookieManager,
            $this->metadataFactory,
            $this->quoteResource
        );
    }

    /**
     * Test observer execute method.
     * @dataProvider executeDataProvider
     *
     * @param bool $result
     * @param string $callCount
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testExecute($result, $callCount)
    {
        $observerMock = $this->createMock(Observer::class);
        $frontendSessionCookieName = 'mage-cache-sessid';
        $this->checkoutSession->expects($this->once())
            ->method('getQuote')
            ->willReturn($this->quoteMock);
        $this->quoteMock->expects($this->once())
            ->method('getData')
            ->with('trigger_private_content_update')
            ->willReturn($result);
        $this->metadataFactory->expects($this->{$callCount}())
            ->method('createCookieMetadata')
            ->willReturn($this->metadata);
        $this->metadata->expects($this->{$callCount}())
            ->method('setPath')
            ->with('/');
        $this->cookieManager->expects($this->{$callCount}())
            ->method('deleteCookie')
            ->with($frontendSessionCookieName, $this->metadata);
        $this->quoteMock->expects($this->{$callCount}())
            ->method('setData')
            ->with('trigger_private_content_update', 0);

        $this->observer->execute($observerMock);
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [true, 'once'],
            [false, 'never']
        ];
    }
}
