<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Module;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Stdlib\String as StringHelper;
use Magento\Framework\Module\Dir\ResolverInterface;

class Dir
{
    /**
     * Modules root directory
     *
     * @var ReadInterface
     */
    protected $_modulesDirectory;

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $_string;

    /**
     * Module directory resolver
     *
     * @var ResolverInterface
     */
    private $dirResolver;

    /**
     * @param Filesystem $filesystem
     * @param StringHelper $string
     * @param ResolverInterface $resolver
     */
    public function __construct(Filesystem $filesystem, StringHelper $string, ResolverInterface $resolver)
    {
        $this->_modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $this->_string = $string;
        $this->dirResolver = $resolver;
    }

    /**
     * Retrieve full path to a directory of certain type within a module
     *
     * @param string $moduleName Fully-qualified module name
     * @param string $type Type of module's directory to retrieve
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDir($moduleName, $type = '')
    {
        if (null === $path = $this->dirResolver->getModulePath($moduleName)) {
            $relativePath = $this->_string->upperCaseWords($moduleName, '_', '/');
            $path = $this->_modulesDirectory->getAbsolutePath($relativePath);
        }
        
        if ($type) {
            if (!in_array($type, ['etc', 'i18n', 'view', 'Controller'])) {
                throw new \InvalidArgumentException("Directory type '{$type}' is not recognized.");
            }
            $path .= '/' . $type;
        }

        return $path;
    }
}
