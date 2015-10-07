<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class IndexerConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $fileList = [];

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $schemaFile;

    protected function setUp()
    {
        $urnResolver = new \Magento\Framework\Config\Dom\UrnResolver();
        $this->schemaFile = $urnResolver->getRealPath('urn:magento:framework:Indexer/etc/indexer.xsd');
    }

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider indexerConfigFileDataProvider
     */
    public function testIndexerConfigFile($file)
    {
        $domConfig = new \Magento\Framework\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($this->schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function indexerConfigFileDataProvider()
    {
        /** @var Filesystem $filesystem */
        $utilityFiles = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Framework\App\Utility\Files');

        $utilityFiles->getConfigFiles('indexer.xml');

        $dataProviderResult = $utilityFiles->getConfigFiles('indexer.xml');
        return $dataProviderResult;
    }
}
