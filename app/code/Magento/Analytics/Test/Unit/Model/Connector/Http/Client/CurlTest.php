<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Analytics\Test\Unit\Model\Connector\Http\Client;

use Magento\Analytics\Model\Connector\Http\ConverterInterface;
use Magento\Analytics\Model\Connector\Http\JsonConverter;
use Magento\Framework\HTTP\Adapter\CurlFactory;
<<<<<<< HEAD

/**
 * A unit test for testing of the CURL HTTP client.
=======
use Magento\Framework\HTTP\ResponseFactory;

/**
 * A unit test for testing of the CURL HTTP client.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class CurlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Analytics\Model\Connector\Http\Client\Curl
     */
<<<<<<< HEAD
    private $subject;
=======
    private $curl;
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl|\PHPUnit_Framework_MockObject_MockObject
     */
<<<<<<< HEAD
    private $curlMock;
=======
    private $curlAdapterMock;
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
<<<<<<< HEAD
     * @var CurlFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $curlFactoryMock;

    /**
     * @var \Magento\Analytics\Model\Connector\Http\ResponseFactory|\PHPUnit_Framework_MockObject_MockObject
=======
     * @var ResponseFactory|\PHPUnit_Framework_MockObject_MockObject
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     */
    private $responseFactoryMock;

    /**
     * @var ConverterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $converterMock;

    /**
<<<<<<< HEAD
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManagerHelper;

    /**
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @return void
     */
    protected function setUp()
    {
<<<<<<< HEAD
        $this->curlMock = $this->getMockBuilder(
=======
        $this->curlAdapterMock = $this->getMockBuilder(
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            \Magento\Framework\HTTP\Adapter\Curl::class
        )
        ->disableOriginalConstructor()
        ->getMock();

        $this->loggerMock = $this->getMockBuilder(
            \Psr\Log\LoggerInterface::class
        )
        ->disableOriginalConstructor()
        ->getMock();
<<<<<<< HEAD
        $this->curlFactoryMock = $this->getMockBuilder(CurlFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->curlFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->curlMock);

        $this->responseFactoryMock = $this->getMockBuilder(
            \Magento\Analytics\Model\Connector\Http\ResponseFactory::class
=======
        $curlFactoryMock = $this->getMockBuilder(CurlFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $curlFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->curlAdapterMock);

        $this->responseFactoryMock = $this->getMockBuilder(
            ResponseFactory::class
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        )
        ->disableOriginalConstructor()
        ->getMock();
        $this->converterMock = $this->createJsonConverter();

<<<<<<< HEAD
        $this->objectManagerHelper =
            new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->subject = $this->objectManagerHelper->getObject(
            \Magento\Analytics\Model\Connector\Http\Client\Curl::class,
            [
                'curlFactory' => $this->curlFactoryMock,
=======
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->curl = $objectManagerHelper->getObject(
            \Magento\Analytics\Model\Connector\Http\Client\Curl::class,
            [
                'curlFactory' => $curlFactoryMock,
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                'responseFactory' => $this->responseFactoryMock,
                'converter' => $this->converterMock,
                'logger' => $this->loggerMock,
            ]
        );
    }

    /**
     * Returns test parameters for request.
     *
     * @return array
     */
    public function getTestData()
    {
        return [
            [
                'data' => [
                    'version' => '1.1',
                    'body'=> ['name' => 'value'],
                    'url' => 'http://www.mystore.com',
<<<<<<< HEAD
                    'headers' => [JsonConverter::CONTENT_TYPE_HEADER],
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                    'method' => \Magento\Framework\HTTP\ZendClient::POST,
                ]
            ]
        ];
    }

    /**
<<<<<<< HEAD
     * @return void
=======
     * @param array $data
     * @return void
     * @throws \Zend_Http_Exception
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @dataProvider getTestData
     */
    public function testRequestSuccess(array $data)
    {
        $responseString = 'This is response.';
        $response = new  \Zend_Http_Response(201, [], $responseString);
<<<<<<< HEAD
        $this->curlMock->expects($this->once())
=======
        $this->curlAdapterMock->expects($this->once())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('write')
            ->with(
                $data['method'],
                $data['url'],
                $data['version'],
<<<<<<< HEAD
                $data['headers'],
                json_encode($data['body'])
            );
        $this->curlMock->expects($this->once())
            ->method('read')
            ->willReturn($responseString);
        $this->curlMock->expects($this->any())
=======
                [$this->converterMock->getContentTypeHeader()],
                json_encode($data['body'])
            );
        $this->curlAdapterMock->expects($this->once())
            ->method('read')
            ->willReturn($responseString);
        $this->curlAdapterMock->expects($this->any())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('getErrno')
            ->willReturn(0);

        $this->responseFactoryMock->expects($this->any())
            ->method('create')
            ->with($responseString)
            ->willReturn($response);

        $this->assertEquals(
            $response,
<<<<<<< HEAD
            $this->subject->request(
                $data['method'],
                $data['url'],
                $data['body'],
                $data['headers'],
=======
            $this->curl->request(
                $data['method'],
                $data['url'],
                $data['body'],
                [$this->converterMock->getContentTypeHeader()],
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                $data['version']
            )
        );
    }

    /**
<<<<<<< HEAD
     * @return void
=======
     * @param array $data
     * @return void
     * @throws \Zend_Http_Exception
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @dataProvider getTestData
     */
    public function testRequestError(array $data)
    {
        $response = new  \Zend_Http_Response(0, []);
<<<<<<< HEAD
        $this->curlMock->expects($this->once())
=======
        $this->curlAdapterMock->expects($this->once())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('write')
            ->with(
                $data['method'],
                $data['url'],
                $data['version'],
<<<<<<< HEAD
                $data['headers'],
                json_encode($data['body'])
            );
        $this->curlMock->expects($this->once())
            ->method('read');
        $this->curlMock->expects($this->atLeastOnce())
            ->method('getErrno')
            ->willReturn(1);
        $this->curlMock->expects($this->atLeastOnce())
=======
                [$this->converterMock->getContentTypeHeader()],
                json_encode($data['body'])
            );
        $this->curlAdapterMock->expects($this->once())
            ->method('read');
        $this->curlAdapterMock->expects($this->atLeastOnce())
            ->method('getErrno')
            ->willReturn(1);
        $this->curlAdapterMock->expects($this->atLeastOnce())
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            ->method('getError')
            ->willReturn('CURL error.');

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with(
                new \Exception(
                    'MBI service CURL connection error #1: CURL error.'
                )
            );

        $this->assertEquals(
            $response,
<<<<<<< HEAD
            $this->subject->request(
                $data['method'],
                $data['url'],
                $data['body'],
                $data['headers'],
=======
            $this->curl->request(
                $data['method'],
                $data['url'],
                $data['body'],
                [$this->converterMock->getContentTypeHeader()],
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                $data['version']
            )
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createJsonConverter()
    {
<<<<<<< HEAD
        $converterMock = $this->getMockBuilder(ConverterInterface::class)
            ->getMockForAbstractClass();
        $converterMock->expects($this->any())->method('toBody')->willReturnCallback(function ($value) {
            return json_encode($value);
        });
        $converterMock->expects($this->any())
            ->method('getContentTypeHeader')
            ->willReturn(JsonConverter::CONTENT_TYPE_HEADER);
=======
        $converterMock = $this->getMockBuilder(JsonConverter::class)
            ->setMethodsExcept(['getContentTypeHeader'])
            ->disableOriginalConstructor()
            ->getMock();
        $converterMock->expects($this->any())->method('toBody')->willReturnCallback(function ($value) {
            return json_encode($value);
        });
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        return $converterMock;
    }
}
