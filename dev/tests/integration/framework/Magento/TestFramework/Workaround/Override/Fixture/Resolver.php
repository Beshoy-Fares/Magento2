<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TestFramework\Workaround\Override\Fixture;

use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Annotation\AdminConfigFixture;
use Magento\TestFramework\Annotation\ConfigFixture;
use Magento\TestFramework\Annotation\DataFixture;
use Magento\TestFramework\Annotation\DataFixtureBeforeTransaction;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Config;
use Magento\TestFramework\Workaround\Override\Fixture\Applier\AdminConfigFixture as AdminConfigFixtureApplier;
use Magento\TestFramework\Workaround\Override\Fixture\Applier\ApplierInterface;
use Magento\TestFramework\Workaround\Override\Fixture\Applier\Base;
use Magento\TestFramework\Workaround\Override\Fixture\Applier\ConfigFixture as ConfigFixtureApplier;
use Magento\TestFramework\Workaround\Override\Fixture\Applier\DataFixture as DataFixtureApplier;
use PHPUnit\Framework\TestCase;

/**
 * Class determines fixture applying according to configurations
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Resolver
{
    /** @var self */
    private static $instance;

    /** @var TestCase */
    private $currentTest;

    /** @var Config */
    private $config;

    /** @var ApplierInterface[] */
    private $appliersList;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string */
    private $currentFixtureType = null;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Get class instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self(Config::getInstance());
        }

        return self::$instance;
    }

    /**
     * Set current test to instance
     *
     * @param TestCase $currentTest
     * @return void
     */
    public function setCurrentTest(?TestCase $currentTest): void
    {
        $this->currentTest = $currentTest;
    }

    /**
     * Get current test
     *
     * @return TestCase|null
     */
    public function getCurrentTest(): ?TestCase
    {
        return $this->currentTest;
    }

    /**
     * Set which fixture type is executed
     *
     * @param null|string $fixtureType
     * @return void
     */
    public function setCurrentFixtureType(?string $fixtureType): void
    {
        $this->currentFixtureType = $fixtureType;
    }

    /**
     * Require fixture wrapper
     *
     * @param string $path
     * @return void
     */
    public function requireDataFixture(string $path): void
    {
        if ($this->currentFixtureType === null) {
            throw new LocalizedException(__('Fixture type is not specified for resolver'));
        }
        /** @var DataFixtureApplier $dataFixtureApplier */
        $dataFixtureApplier = $this->getApplier($this->getCurrentTest(), $this->currentFixtureType);
        $fixture = $this->processFixturePath($this->currentTest, $dataFixtureApplier->replace($path));

        is_callable($fixture) ? call_user_func($fixture) : require $fixture;
    }

    /**
     * Apply override configurations to config fixtures list
     *
     * @param TestCase $test
     * @param array $fixtures
     * @param string $fixtureType
     * @return array
     */
    public function applyConfigFixtures(TestCase $test, array $fixtures, string $fixtureType): array
    {
        return $this->getApplier($test, $fixtureType)->apply($fixtures);
    }

    /**
     * Apply override configurations to data fixtures list
     *
     * @param TestCase $test
     * @param array $fixtures
     * @param string $fixtureType
     * @return array
     */
    public function applyDataFixtures(TestCase $test, array $fixtures, string $fixtureType): array
    {
        $result = [];
        $fixtures = $this->getApplier($test, $fixtureType)->apply($fixtures);

        foreach ($fixtures as $fixture) {
            $result[] = $this->processFixturePath($test, $fixture);
        }

        return $result;
    }

    /**
     * Get ComponentRegistrar object
     *
     * @return ComponentRegistrarInterface
     */
    protected function getComponentRegistrar(): ComponentRegistrarInterface
    {
        return $this->objectManager->get(ComponentRegistrar::class);
    }

    /**
     * Get applier with prepared config by annotation type
     *
     * @param TestCase $test
     * @param string $fixtureType
     * @return ApplierInterface
     */
    private function getApplier(TestCase $test, string $fixtureType): ApplierInterface
    {
        if (!isset($this->appliersList[$fixtureType])) {
            $this->appliersList[$fixtureType] = $this->getApplierByFixtureType($fixtureType);
        }
        /** @var Base $applier */
        $applier = $this->appliersList[$fixtureType];
        $applier->setClassConfig($this->config->getClassConfig($test, $fixtureType));
        $applier->setMethodConfig($this->config->getMethodConfig($test, $fixtureType));
        $applier->setDataSetConfig(
            $test->dataName()
                ? $this->config->getDataSetConfig($test, $fixtureType)
                : []
        );

        return $applier;
    }

    /**
     * Get appropriate fixture applier according to fixture type
     *
     * @param string $fixtureType
     * @return ApplierInterface
     */
    private function getApplierByFixtureType(string $fixtureType): ApplierInterface
    {
        switch ($fixtureType) {
            case DataFixture::ANNOTATION:
            case DataFixtureBeforeTransaction::ANNOTATION:
                $applier = $this->objectManager->get(DataFixtureApplier::class);
                break;
            case ConfigFixture::ANNOTATION:
                $applier = $this->objectManager->get(ConfigFixtureApplier::class);
                break;
            case AdminConfigFixture::ANNOTATION:
                $applier = $this->objectManager->get(AdminConfigFixtureApplier::class);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported fixture type %s provided', $fixtureType));
        }

        return $applier;
    }

    /**
     * Converts fixture path.
     *
     * @param TestCase $test
     * @param string $fixture
     * @return string|array
     * @throws LocalizedException
     */
    private function processFixturePath(TestCase $test, string $fixture)
    {
        if (strpos($fixture, '\\') !== false) {
            // usage of a single directory separator symbol streamlines search across the source code
            throw new LocalizedException(__('Directory separator "\\" is prohibited in fixture declaration.'));
        }

        $fixtureMethod = [get_class($test), $fixture];
        if (is_callable($fixtureMethod)) {
            $result = $fixtureMethod;
        } elseif ($this->isModuleAnnotation($fixture)) {
            $result = $this->getModulePath($fixture);
        } else {
            $result = INTEGRATION_TESTS_DIR . '/testsuite/' . $fixture;
        }

        return $result;
    }

    /**
     * Check is the Annotation like Magento_InventoryApi::Test/_files/products.php
     *
     * @param string $fixture
     * @return bool
     */
    private function isModuleAnnotation(string $fixture): bool
    {
        return (strpos($fixture, '::') !== false);
    }

    /**
     * Resolve the fixture module annotation path.
     *
     * @param string $fixture
     * @return string
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getModulePath(string $fixture): string
    {
        $componentRegistrar = $this->getComponentRegistrar();
        [$moduleName, $fixtureFile] = explode('::', $fixture, 2);

        $modulePath = $componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);

        if ($modulePath === null) {
            throw new LocalizedException(__('Can\'t find registered Module with name %1 .', $moduleName));
        }

        return $modulePath . '/' . ltrim($fixtureFile, '/');
    }
}
