<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Setup\Module\Dependency\ServiceLocator;

/**
 * Command for showing numbers of dependencies on Magento Framework
 */
class DependenciesShowFrameworkCommand extends AbstractDependenciesCommand
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ComponentRegistrarInterface
     */
    private $registrar;

    /**
     * Constructor
     *
     * @param DirectoryList $directoryList
     * @param ComponentRegistrarInterface $registrar
     */
    public function __construct(DirectoryList $directoryList, ComponentRegistrarInterface $registrar)
    {
        $this->directoryList = $directoryList;
        $this->registrar = $registrar;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Shows number of dependencies on Magento framework')
            ->setName('info:dependencies:show-framework');
        parent::configure();
    }

    /**
     * Return default output filename for framework dependencies report
     *
     * @return string
     */
    protected function getDefaultOutputFilename()
    {
        return 'framework-dependencies.csv';
    }

    /**
     * Build Framework dependencies report
     *
     * @param string $outputPath
     * @return void
     */
    protected function buildReport($outputPath)
    {
        $filePaths = $this->registrar->getPaths(ComponentRegistrar::MODULE);

        $filesForParse = Files::init()->getFiles($filePaths, '*');
        $configFiles = Files::init()->getConfigFiles('module.xml', [], false);

        ServiceLocator::getFrameworkDependenciesReportBuilder()->build(
            [
                'parse' => [
                    'files_for_parse' => $filesForParse,
                    'config_files' => $configFiles,
                    'declared_namespaces' => Files::init()->getNamespaces(),
                ],
                'write' => ['report_filename' => $outputPath],
            ]
        );
    }
}
