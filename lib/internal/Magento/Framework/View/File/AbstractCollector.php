<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\View\File;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use Magento\Framework\View\File\Factory as FileFactory;
use Magento\Framework\View\Helper\PathPattern as PathPatternHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Abstract file collector
 */
abstract class AbstractCollector implements CollectorInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * @var \Magento\Framework\View\File\Factory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\View\Helper\PathPattern
     */
    protected $pathPatternHelper;

    /**
     * @var string
     */
    protected $subDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Factory $fileFactory
     * @param PathPatternHelper $pathPatternHelper
     * @param string $subDir
     */
    public function __construct(
        Filesystem $filesystem,
        FileFactory $fileFactory,
        PathPatternHelper $pathPatternHelper,
        $subDir = ''
    ) {
        $this->fileFactory = $fileFactory;
        $this->pathPatternHelper = $pathPatternHelper;
        $this->subDir = $subDir ? $subDir . '/' : '';
    }

    /**
     * Get scope directory of this file collector
     *
     * @return string
     */
    protected function getScopeDirectory()
    {
        // TODO: remove this method in scope of MAGETWO-42266
        return DirectoryList::ROOT;
    }
}
