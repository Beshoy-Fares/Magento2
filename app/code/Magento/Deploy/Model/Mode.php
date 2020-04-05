<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Magento\Deploy\Model;

use Magento\Config\Console\Command\ConfigSet\ProcessorFacadeFactory;
use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Magento\Deploy\App\Mode\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Console\MaintenanceModeEnabler;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A class to manage Magento modes
 *
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 * @SuppressWarnings("PMD.ExcessiveParameterList")
 */
class Mode
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * The factory for processor facade.
     *
     * @var ProcessorFacadeFactory
     */
    private $processorFacadeFactory;

    /**
     * Emulator adminhtml area for CLI command.
     *
     * @var EmulatedAdminhtmlAreaProcessor
     */
    private $emulatedAreaProcessor;

    /**
     * @var MaintenanceModeEnabler
     */
    private $maintenanceModeEnabler;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Writer $writer
     * @param Reader $reader
     * @param MaintenanceMode $maintenanceMode deprecated, use $maintenanceModeEnabler instead
     * @param Filesystem $filesystem
     * @param ConfigProvider $configProvider
     * @param ProcessorFacadeFactory $processorFacadeFactory
     * @param EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor
     * @param MaintenanceModeEnabler $maintenanceModeEnabler
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        Writer $writer,
        Reader $reader,
        MaintenanceMode $maintenanceMode,
        Filesystem $filesystem,
        ConfigProvider $configProvider = null,
        ProcessorFacadeFactory $processorFacadeFactory = null,
        EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor = null,
        MaintenanceModeEnabler $maintenanceModeEnabler = null
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->writer = $writer;
        $this->reader = $reader;
        $this->filesystem = $filesystem;

        $this->configProvider =
            $configProvider ?: ObjectManager::getInstance()->get(ConfigProvider::class);
        $this->processorFacadeFactory =
            $processorFacadeFactory ?: ObjectManager::getInstance()->get(ProcessorFacadeFactory::class);
        $this->emulatedAreaProcessor =
            $emulatedAreaProcessor ?: ObjectManager::getInstance()->get(EmulatedAdminhtmlAreaProcessor::class);
        $this->maintenanceModeEnabler =
            $maintenanceModeEnabler ?: ObjectManager::getInstance()->get(MaintenanceModeEnabler::class);
    }

    /**
     * Enable production mode
     *
     * @throws LocalizedException
     * @return void
     * @throws \Throwable if error occurred
     */
    public function enableProductionMode()
    {
        $this->maintenanceModeEnabler->executeInMaintenanceMode(
            function () {
                $previousMode = $this->getMode();
                try {
                    // We have to turn on production mode before generation.
                    // We need this to enable generation of the "min" files.
                    $this->setStoreMode(State::MODE_PRODUCTION);
                    $this->filesystem->regenerateStaticFiles($this->output, true);
                } catch (LocalizedException $e) {
                    // We have to return store mode to previous state in case of error.
                    $this->setStoreMode($previousMode);
                    throw $e;
                }
            },
            $this->output,
            false
        );
    }

    /**
     * Only lock static resource locations and set store mode, without handling static content
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function enableProductionModeMinimal()
    {
        $this->setStoreMode(State::MODE_PRODUCTION);
    }

    /**
     * Enable Developer mode
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function enableDeveloperMode()
    {
        $this->filesystem->cleanupFilesystem(
            [
                DirectoryList::CACHE,
                DirectoryList::GENERATED_CODE,
                DirectoryList::GENERATED_METADATA,
                DirectoryList::TMP_MATERIALIZATION_DIR,
                DirectoryList::STATIC_VIEW,
            ]
        );
        $this->setStoreMode(State::MODE_DEVELOPER);
    }

    /**
     * Enable Default mode.
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function enableDefaultMode()
    {
        $this->filesystem->cleanupFilesystem(
            [
                DirectoryList::CACHE,
                DirectoryList::GENERATED_CODE,
                DirectoryList::GENERATED_METADATA,
                DirectoryList::TMP_MATERIALIZATION_DIR,
                DirectoryList::STATIC_VIEW,
            ]
        );
        $this->setStoreMode(State::MODE_DEFAULT);
    }

    /**
     * Get current mode information
     *
     * @return string
     * @throws \Exception
     */
    public function getMode()
    {
        $env = $this->reader->load();
        return $env[State::PARAM_MODE] ?? null;
    }

    /**
     * Store mode in env.php
     *
     * @param string $mode
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    protected function setStoreMode($mode)
    {
        $this->saveAppConfigs($mode);
        $data = [
            ConfigFilePool::APP_ENV => [
                State::PARAM_MODE => $mode
            ]
        ];
        $this->writer->saveConfig($data);
    }

    /**
     * Save application configs while switching mode
     *
     * @param string $mode
     * @return void
     * @throws \Exception
     */
    private function saveAppConfigs($mode)
    {
        $configs = $this->configProvider->getConfigs($this->getMode(), $mode);
        foreach ($configs as $path => $value) {
            $this->emulatedAreaProcessor->process(
                function () use ($path, $value) {
                    $this->processorFacadeFactory->create()->processWithLockTarget(
                        $path,
                        $value,
                        ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                        null,
                        true
                    );
                }
            );
            $this->output->writeln('Config "' . $path . ' = ' . $value . '" has been saved.');
        }
    }
}
