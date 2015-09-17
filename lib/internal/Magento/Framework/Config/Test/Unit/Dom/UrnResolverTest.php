<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Config\Test\Unit\Dom;

use \Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Component\ComponentRegistrar;

class UrnResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrnResolver
     */
    protected $urnResolver;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    public function setUp()
    {
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->urnResolver = $this->objectManagerHelper->getObject('Magento\Framework\Config\Dom\UrnResolver');
    }

    public function testGetRealPathNoUrn()
    {
        $xsdPath = '../../testPath/test.xsd';
        $result = $this->urnResolver->getRealPath($xsdPath);
        $this->assertSame($xsdPath, $result, 'XSD paths does not match.');
    }

    public function testGetRealPathWithFrameworkUrn()
    {
        $xsdUrn = 'urn:magento:library:framework:Config/Test/Unit/_files/sample.xsd';
        $xsdPath = realpath(dirname(__DIR__)) . '/_files/sample.xsd';
        $result = $this->urnResolver->getRealPath($xsdUrn);
        $this->assertSame($xsdPath, $result, 'XSD paths does not match.');
    }

    public function testGetRealPathWithModuleUrn()
    {
        $xsdUrn = 'urn:magento:module:customer:etc/address_formats.xsd';
        $componentRegistrar = new ComponentRegistrar();
        $xsdPath = $componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magento_Customer')
            . '/etc/address_formats.xsd';
        $result = $this->urnResolver->getRealPath($xsdUrn);
        $this->assertSame($xsdPath, $result, 'XSD paths does not match.');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Unsupported format of schema location: urn:magento:test:test:etc/test_test.xsd
     */
    public function testGetRealPathWrongSection()
    {
        $xsdUrn = 'urn:magento:test:test:etc/test_test.xsd';
        $this->urnResolver->getRealPath($xsdUrn);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Could not locate schema: 'urn:magento:module:test:testfile.xsd' at '/testfile.xsd'
     */
    public function testGetRealPathWrongModule()
    {
        $xsdUrn = 'urn:magento:module:test:testfile.xsd';
        $this->urnResolver->getRealPath($xsdUrn);
    }
}
