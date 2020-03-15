<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Controller\Section;

use Magento\Customer\Controller\Section\Load;
use Magento\Customer\CustomerData\SectionPoolInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zend\Http\AbstractMessage;
use Zend\Http\Response;

class LoadTest extends TestCase
{
    /**
     * @var Load
     */
    private $loadAction;

    /**
     * @var JsonFactory|MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var SectionPoolInterface|MockObject
     */
    private $sectionPoolMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var Json|MockObject
     */
    private $resultJsonMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    protected function setUp()
    {
        $this->resultJsonFactoryMock = $this->createMock(JsonFactory::class);
        $this->sectionPoolMock = $this->getMockForAbstractClass(SectionPoolInterface::class);
        $this->escaperMock = $this->createMock(Escaper::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->resultJsonMock = $this->createMock(Json::class);

        $this->loadAction = new Load(
            $this->requestMock,
            $this->resultJsonFactoryMock,
            $this->sectionPoolMock,
            $this->escaperMock
        );
    }

    /**
     * @param string $sectionNames
     * @param bool $forceNewSectionTimestamp
     * @param string[] $sectionNamesAsArray
     * @param bool $forceNewTimestamp
     * @dataProvider executeDataProvider
     */
    public function testExecute($sectionNames, $forceNewSectionTimestamp, $sectionNamesAsArray, $forceNewTimestamp)
    {
        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJsonMock);
        $this->resultJsonMock->expects($this->exactly(2))
            ->method('setHeader')
            ->withConsecutive(
                ['Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store'],
                ['Pragma', 'no-cache']
            );

        $this->requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(['sections'], ['force_new_section_timestamp'])
            ->willReturnOnConsecutiveCalls($sectionNames, $forceNewSectionTimestamp);

        $this->sectionPoolMock->expects($this->once())
            ->method('getSectionsData')
            ->with($sectionNamesAsArray, $forceNewTimestamp)
            ->willReturn([
                'message' => 'some message',
                'someKey' => 'someValue'
            ]);

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'message' => 'some message',
                'someKey' => 'someValue'
            ])
            ->willReturn($this->resultJsonMock);

        $this->loadAction->execute();
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [
                'sectionNames' => 'sectionName1,sectionName2,sectionName3',
                'forceNewSectionTimestamp' => 'forceNewSectionTimestamp',
                'sectionNamesAsArray' => ['sectionName1', 'sectionName2', 'sectionName3'],
                'forceNewTimestamp' => true
            ],
            [
                'sectionNames' => null,
                'forceNewSectionTimestamp' => null,
                'sectionNamesAsArray' => null,
                'forceNewTimestamp' => false
            ],
        ];
    }

    public function testExecuteWithThrowException()
    {
        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJsonMock);

        $this->resultJsonMock->expects($this->exactly(2))
            ->method('setHeader')
            ->withConsecutive(
                ['Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store'],
                ['Pragma', 'no-cache']
            );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('sections')
            ->willThrowException(new \Exception('Some Message'));

        $this->resultJsonMock->expects($this->once())
            ->method('setStatusHeader')
            ->with(Response::STATUS_CODE_400, AbstractMessage::VERSION_11, 'Bad Request');

        $this->escaperMock->expects($this->once())
            ->method('escapeHtml')
            ->with('Some Message')
            ->willReturn('Some Message');

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(['message' => 'Some Message'])
            ->willReturn($this->resultJsonMock);

        $this->loadAction->execute();
    }
}
