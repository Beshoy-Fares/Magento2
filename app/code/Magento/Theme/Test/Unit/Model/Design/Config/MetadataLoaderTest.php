<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Test\Unit\Model\Design\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ScopeFallbackResolverInterface;
use Magento\Theme\Model\Design\Config\MetadataLoader;
use Magento\Theme\Model\Design\Config\MetadataProvider;

class MetadataLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataLoader
     */
    protected $model;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var MetadataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataProvider;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var ScopeFallbackResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeFallbackResolver;

    protected function setUp()
    {
        $this->request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataProvider = $this->getMockBuilder('Magento\Theme\Model\Design\Config\MetadataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getMockForAbstractClass();

        $this->scopeFallbackResolver = $this->getMockBuilder('Magento\Framework\App\ScopeFallbackResolverInterface')
            ->getMockForAbstractClass();

        $this->model = new MetadataLoader(
            $this->request,
            $this->metadataProvider,
            $this->scopeConfig,
            $this->scopeFallbackResolver
        );
    }

    /**
     * @param string $scope
     * @param string $scopeId
     * @param string $showFallbackReset
     * @dataProvider dataProviderGetData
     */
    public function testGetData(
        $scope,
        $scopeId,
        $showFallbackReset
    ) {
        $metadataSrc = [
            'data_name' => [
                'path' => 'name/data_path',
                'fieldset' => 'theme',
            ],
            'metadata_name' => [
                'path' => 'name/metadata_path',
                'fieldset' => 'theme',
            ],
        ];

        $this->request->expects($this->exactly(2))
            ->method('getParam')
            ->willReturnMap([
                ['scope', null, $scope],
                ['scope_id', null, $scopeId],
            ]);

        $this->metadataProvider->expects($this->once())
            ->method('get')
            ->willReturn($metadataSrc);

        $this->scopeFallbackResolver->expects($this->exactly(2))
            ->method('getFallbackScope')
            ->with($scope, $scopeId)
            ->willReturn([$scope, $scopeId]);

        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap([
                ['name/data_path', $scope, $scopeId, 'data_value'],
                ['name/metadata_path', $scope, $scopeId, 'metadata_value'],
            ]);

        $result = $this->model->getData();

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('theme', $result));
        $this->assertTrue(is_array($result['theme']));
        $this->assertTrue(array_key_exists('fields', $result['theme']));
        $this->assertTrue(is_array($result['theme']['fields']));

        $this->assertTrue(array_key_exists('data_name', $result['theme']['fields']));
        $this->assertEquals('data_value', $result['theme']['fields']['data_name']['default']);
        $this->assertEquals($showFallbackReset, $result['theme']['fields']['data_name']['showFallbackReset']);

        $this->assertTrue(array_key_exists('metadata_name', $result['theme']['fields']));
        $this->assertEquals('metadata_value', $result['theme']['fields']['metadata_name']['default']);
        $this->assertEquals($showFallbackReset, $result['theme']['fields']['metadata_name']['showFallbackReset']);
    }

    /**
     * @return array
     */
    public function dataProviderGetData()
    {
        return [
            ['default', 0, false],
            ['websites', 1, true],
        ];
    }
}
