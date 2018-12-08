<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======

declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Eav\Model\Entity;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Locale\ResolverInterface;

class AttributeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ResolverInterface
     */
    private $_localeResolver;

<<<<<<< HEAD
=======
    /**
     * {@inheritdoc}
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->attribute = $this->objectManager->get(Attribute::class);
        $this->_localeResolver = $this->objectManager->get(ResolverInterface::class);
    }

<<<<<<< HEAD
=======
    /**
     * {@inheritdoc}
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    protected function tearDown()
    {
        $this->attribute = null;
        $this->objectManager = null;
        $this->_localeResolver = null;
    }

    /**
     * @param string $defaultValue
     * @param string $backendType
     * @param string $locale
     * @param string $expected
     * @dataProvider beforeSaveDataProvider
     * @throws
     */
    public function testBeforeSave($defaultValue, $backendType, $locale, $expected)
    {
        $this->attribute->setDefaultValue($defaultValue);
        $this->attribute->setBackendType($backendType);
        $this->_localeResolver->setLocale($locale);
        $this->attribute->beforeSave();

        $this->assertEquals($expected, $this->attribute->getDefaultValue());
    }

<<<<<<< HEAD
=======
    /**
     * Data provider for beforeSaveData.
     *
     * @return array
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    public function beforeSaveDataProvider()
    {
        return [
            ['21/07/18', 'datetime', 'en_AU', '2018-07-21 00:00:00'],
            ['07/21/18', 'datetime', 'en_US', '2018-07-21 00:00:00'],
            ['21/07/18', 'datetime', 'fr_FR', '2018-07-21 00:00:00'],
            ['21/07/18', 'datetime', 'de_DE', '2018-07-21 00:00:00'],
            ['21/07/18', 'datetime', 'uk_UA', '2018-07-21 00:00:00'],
            ['100.50', 'decimal', 'en_US', '100.50'],
            ['100,50', 'decimal', 'uk_UA', '100.5'],
        ];
    }

    /**
     * @param string $defaultValue
     * @param string $backendType
     * @param string $locale
     * @param string $expected
     * @dataProvider beforeSaveErrorDataDataProvider
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testBeforeSaveErrorData($defaultValue, $backendType, $locale, $expected)
    {
        $this->attribute->setDefaultValue($defaultValue);
        $this->attribute->setBackendType($backendType);
        $this->_localeResolver->setLocale($locale);
        $this->attribute->beforeSave();

        $this->expectExceptionMessage($expected);
    }

<<<<<<< HEAD
=======
    /**
     * Data provider for beforeSaveData with error result.
     *
     * @return array
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    public function beforeSaveErrorDataDataProvider()
    {
        return [
            'wrong date for Australia' => ['32/38', 'datetime', 'en_AU', 'Invalid default date'],
            'wrong date for States' => ['32/38', 'datetime', 'en_US', 'Invalid default date'],
            'wrong decimal separator for US' => ['100,50', 'decimal', 'en_US', 'Invalid default decimal value'],
            'wrong decimal separator for UA' => ['100.50', 'decimal', 'uk_UA', 'Invalid default decimal value'],
        ];
    }
}
