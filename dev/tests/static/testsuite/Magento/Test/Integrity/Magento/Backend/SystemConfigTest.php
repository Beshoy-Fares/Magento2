<?php
/**
 * Find "backend/system.xml" files and validate them
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Test\Integrity\Magento\Backend;

class SystemConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $configFile
             */
            function ($configFile) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($configFile));
                $schema = \Magento\Framework\Test\Utility\Files::init()->getPathToSource() .
                    '/vendor/magento/Magento/Backend/etc/system_file.xsd';
                $errors = \Magento\Framework\Config\Dom::validateDomDocument($dom, $schema);
                if ($errors) {
                    $this->fail('XML-file has validation errors:' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
                }
            },
            \Magento\Framework\Test\Utility\Files::init()->getConfigFiles('adminhtml/system.xml', [])
        );
    }
}
