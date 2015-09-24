<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Utility;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\View\Design\Theme\ThemePackageList;

/**
 * A helper to gather specific kind of files in Magento application
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Files
{
    /**
     * File types offset flags
     */
    const INCLUDE_APP_CODE = 1;
    const INCLUDE_TESTS = 2;
    const INCLUDE_DEV_TOOLS = 4;
    const INCLUDE_TEMPLATES = 8;
    const INCLUDE_LIBS = 16;
    const INCLUDE_PUB_CODE = 32;
    const INCLUDE_DATA_SET = 64;


    /**
     * Component registrar
     *
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\App\Utility\Files
     */
    protected static $_instance = null;

    /**
     * In-memory cache for the data sets
     *
     * @var array
     */
    protected static $_cache = [];

    /**
     * Dir search for registered components
     *
     * @var DirSearch
     */
    private $dirSearch;

    /**
     * Theme list for registered themes
     *
     * @var ThemePackageList
     */
    private $themePackageList;

    /**
     * Setter for an instance of self
     *
     * Also can unset the current instance, if no arguments are specified
     *
     * @param Files|null $instance
     * @return void
     */
    public static function setInstance(Files $instance = null)
    {
        self::$_instance = $instance;
    }

    /**
     * Getter for an instance of self
     *
     * @return \Magento\Framework\App\Utility\Files
     * @throws \Exception when there is no instance set
     */
    public static function init()
    {
        if (!self::$_instance) {
            throw new \Exception('Instance is not set yet.');
        }
        return self::$_instance;
    }

    /**
     * Compose PHPUnit's data sets that contain each file as the first argument
     *
     * @param array $files
     * @return array
     */
    public static function composeDataSets(array $files)
    {
        $result = [];
        foreach ($files as $file) {
            $result[$file] = [$file];
        }
        return $result;
    }

    /**
     * Set path to source code
     *
     * @param ComponentRegistrar $componentRegistrar
     * @param DirSearch $dirSearch
     * @param ThemePackageList $themePackageList
     */
    public function __construct(
        ComponentRegistrar $componentRegistrar,
        DirSearch $dirSearch,
        ThemePackageList $themePackageList
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->dirSearch = $dirSearch;
        $this->themePackageList = $themePackageList;
    }

    /**
     * Get test directories in modules
     *
     * @return array
     */
    private function getModuleTestDirs()
    {
        $moduleTestDirs = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $moduleTestDirs[] = str_replace('\\', '/', '#' . $moduleDir . '/Test#');
        }
        return $moduleTestDirs;
    }

    /**
     * Get registration files in modules
     *
     * @return array
     */
    private function getModuleRegistrationFiles()
    {
        $moduleRegistrationFiles = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $moduleRegistrationFiles[] = str_replace('\\', '/', '#' . $moduleDir . '/registration.php#');
        }
        return $moduleRegistrationFiles;
    }

    /**
     * Get test directories in libraries
     *
     * @return array
     */
    private function getLibraryTestDirs()
    {
        $libraryTestDirs = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::LIBRARY) as $libraryDir) {
            $libraryTestDirs[] = str_replace('\\', '/', '#' . $libraryDir . '/Test#');
            $libraryTestDirs[] = str_replace('\\', '/', '#' . $libraryDir) . '/[\\w]+/Test#';
        }
        return $libraryTestDirs;
    }

    /**
     * Get registration files in libraries
     *
     * @return array
     */
    private function getLibraryRegistrationFiles()
    {
        $libraryRegistrationFiles = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::LIBRARY) as $libraryDir) {
            $libraryRegistrationFiles[] = str_replace('\\', '/', '#' . $libraryDir . '/registration.php#');
        }
        return $libraryRegistrationFiles;
    }

    /**
     * Getter for BP global constant
     *
     * @return string
     */
    public function getPathToSource()
    {
        return BP;
    }

    /**
     * Returns list of files, where expected to have class declarations
     *
     * @param int $flags
     * @return array
     */
    public function getClassFiles($flags = 0)
    {
        // Sets default value
        if ($flags === 0) {
            $flags = self::INCLUDE_APP_CODE
                | self::INCLUDE_TESTS
                | self::INCLUDE_DEV_TOOLS
                | self::INCLUDE_LIBS
                | self::INCLUDE_DATA_SET;
        }
        $key = __METHOD__ . BP . $flags;
        if (!isset(self::$_cache[$key])) {
            $files = [];

            $files = array_merge($files, $this->getModuleFiles($flags));
            $files = array_merge($files, $this->getTestFiles($flags));
            $files = array_merge($files, $this->getDevToolsFiles($flags));
            $files = array_merge($files, $this->getTemplateFiles($flags));
            $files = array_merge($files, $this->getLibraryFiles($flags));
            $files = array_merge($files, $this->getPubFiles($flags));
            self::$_cache[$key] = $files;
        }
        if ($flags & self::INCLUDE_DATA_SET) {
            return self::composeDataSets(self::$_cache[$key]);
        }
        return self::$_cache[$key];
    }

    /**
     * Return array with all template files
     *
     * @param $flags
     * @return array
     */
    private function getTemplateFiles($flags)
    {
        if ($flags & self::INCLUDE_TEMPLATES) {
            return $this->getPhtmlFiles(false, false);
        }
        return [];
    }

    /**
     * Return array with all php files related to library
     *
     * @param $flags
     * @return array
     */
    private function getLibraryFiles($flags){
        if ($flags & self::INCLUDE_LIBS) {
            return $this->getFilesSubset(
                $this->componentRegistrar->getPaths(ComponentRegistrar::LIBRARY),
                '*.php',
                array_merge($this->getLibraryTestDirs(), $this->getLibraryRegistrationFiles())
            );
        }
        return [];
    }

    /**
     * Return array with all php files related to pub
     *
     * @param $flags
     * @return array
     */
    private function getPubFiles($flags){
        if ($flags & self::INCLUDE_PUB_CODE) {
            return array_merge(
                glob(BP . '/*.php', GLOB_NOSORT),
                glob(BP . '/pub/*.php', GLOB_NOSORT)
            );
        }
        return [];
    }

    /**
     * Return array with all php files related to dev tools
     *
     * @param int $flags
     * @return array
     */
    private function getDevToolsFiles($flags){
        if ($flags & self::INCLUDE_DEV_TOOLS) {
            return $this->getFilesSubset([BP . '/dev/tools/Magento'], '*.php', []);
        }
        return [];
    }

    /**
     * Return array with all php files related to modules
     *
     * @param int $flags
     * @return array
     */
    private function getModuleFiles($flags)
    {
        if ($flags & self::INCLUDE_APP_CODE) {
            return $this->getFilesSubset(
                $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE),
                '*.php',
                array_merge($this->getModuleTestDirs(), $this->getModuleRegistrationFiles())
            );
        }
        return [];
    }

    /**
     * Return array with all test files
     *
     * @param int $flags
     * @return array
     */
    private function getTestFiles($flags)
    {
        if ($flags & self::INCLUDE_TESTS) {
            $testDirs = [
                BP . '/dev/tests',
                BP . '/setup/src/Magento/Setup/Test',
            ];
            $moduleTestDir = [];
            foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
                $moduleTestDir[] = $moduleDir . '/Test';
            }
            $testDirs = array_merge($testDirs, $moduleTestDir, $this->getLibraryTestDirs());
            return self::getFiles($testDirs, '*.php');
        }
        return [];
    }
    /**
     * Returns list of xml files, used by Magento application
     *
     * @return array
     */
    public function getXmlFiles()
    {
        return array_merge(
            $this->getMainConfigFiles(),
            $this->getLayoutFiles(),
            $this->getPageLayoutFiles(),
            $this->getConfigFiles(),
            $this->getDiConfigs(true),
            $this->getLayoutConfigFiles(),
            $this->getPageTypeFiles()
        );
    }

    /**
     * Retrieve all config files, that participate (or have a chance to participate) in composing main config
     *
     * @param bool $asDataSet
     * @return array
     */
    public function getMainConfigFiles($asDataSet = true)
    {

        $cacheKey = __METHOD__ . '|' . BP . '|' . serialize(func_get_args());
        if (!isset(self::$_cache[$cacheKey])) {
            $configXmlPaths = [];
            foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
                $configXmlPaths[] = $moduleDir . '/etc/config.xml';
                // Module DB-specific configs, e.g. config.mysql4.xml
                $configXmlPaths[] = $moduleDir . '/etc/config.*.xml';
            }
            $globPaths = ['app/etc/config.xml', 'app/etc/*/config.xml'];
            $globPaths = array_merge($globPaths, $configXmlPaths);
            $files = [];
            foreach ($globPaths as $globPath) {
                $files = array_merge($files, glob(BP . '/' . $globPath));
            }
            self::$_cache[$cacheKey] = $files;
        }
        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$cacheKey]);
        }
        return self::$_cache[$cacheKey];
    }

    /**
     * Returns list of configuration files, used by Magento application
     *
     * @param string $fileNamePattern
     * @param array $excludedFileNames
     * @param bool $asDataSet
     * @return array
     * @codingStandardsIgnoreStart
     */
    public function getConfigFiles(
        $fileNamePattern = '*.xml',
        $excludedFileNames = ['wsdl.xml', 'wsdl2.xml', 'wsi.xml'],
        $asDataSet = true
    ) {
        $cacheKey = __METHOD__ . '|' . BP . '|' . serialize(func_get_args());
        if (!isset(self::$_cache[$cacheKey])) {
            $files = $this->dirSearch->collectFiles(ComponentRegistrar::MODULE, "/etc/{$fileNamePattern}");
            $files = array_filter(
                $files,
                function ($file) use ($excludedFileNames) {
                    return !in_array(basename($file), $excludedFileNames);
                }
            );
            self::$_cache[$cacheKey] = $files;
        }
        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$cacheKey]);
        }
        return self::$_cache[$cacheKey];
    }
    // @codingStandardsIgnoreEnd

    /**
     * Returns a list of configuration files found under theme directories.
     *
     * @param string $fileNamePattern
     * @param bool $asDataSet
     * @return array
     */
    public function getLayoutConfigFiles($fileNamePattern = '*.xml', $asDataSet = true)
    {
        $cacheKey = __METHOD__ . '|' . BP . '|' . serialize(func_get_args());
        if (!isset(self::$_cache[$cacheKey])) {
            self::$_cache[$cacheKey] = $this->dirSearch->collectFiles(
                ComponentRegistrar::THEME,
                "/etc/{$fileNamePattern}"
            );
        }
        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$cacheKey]);
        }
        return self::$_cache[$cacheKey];
    }

    /**
     * Returns list of page configuration and generic layout files, used by Magento application modules
     *
     * An incoming array can contain the following items
     * array (
     *     'namespace'      => 'namespace_name',
     *     'module'         => 'module_name',
     *     'area'           => 'area_name',
     *     'theme'          => 'theme_name',
     *     'include_code'   => true|false,
     *     'include_design' => true|false,
     *     'with_metainfo'  => true|false,
     * )
     *
     * @param array $incomingParams
     * @param bool $asDataSet
     * @return array
     */
    public function getLayoutFiles($incomingParams = [], $asDataSet = true)
    {
        return $this->getLayoutXmlFiles('layout', $incomingParams, $asDataSet);
    }

    /**
     * Returns list of page layout files, used by Magento application modules
     *
     * An incoming array can contain the following items
     * array (
     *     'namespace'      => 'namespace_name',
     *     'module'         => 'module_name',
     *     'area'           => 'area_name',
     *     'theme'          => 'theme_name',
     *     'include_code'   => true|false,
     *     'include_design' => true|false,
     *     'with_metainfo'  => true|false,
     * )
     *
     * @param array $incomingParams
     * @param bool $asDataSet
     * @return array
     */
    public function getPageLayoutFiles($incomingParams = [], $asDataSet = true)
    {
        return $this->getLayoutXmlFiles('page_layout', $incomingParams, $asDataSet);
    }

    /**
     * @param string $location
     * @param array $incomingParams
     * @param bool $asDataSet
     * @return array
     */
    protected function getLayoutXmlFiles($location, $incomingParams = [], $asDataSet = true)
    {
        $params = [
            'namespace' => '*',
            'module' => '*',
            'area' => '*',
            'theme_path' => '*/*',
            'include_code' => true,
            'include_design' => true,
            'with_metainfo' => false
        ];
        foreach (array_keys($params) as $key) {
            if (isset($incomingParams[$key])) {
                $params[$key] = $incomingParams[$key];
            }
        }
        $cacheKey = md5(BP . '|' . $location . '|' . implode('|', $params));

        if (!isset(self::$_cache[__METHOD__][$cacheKey])) {
            $this->populateLayoutXmlCache(__METHOD__, $params, $location, $cacheKey);
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[__METHOD__][$cacheKey]);
        }
        return self::$_cache[__METHOD__][$cacheKey];
    }

    /**
     * Helper method for getLayoutXmlFiles() to find the layout xml file and cache it
     *
     * @param string $method
     * @param string $params
     * @param string $location
     * @param string $cacheKey
     * @return void
     */
    private function populateLayoutXmlCache($method, $params, $location, $cacheKey)
    {
        $files = [];
        $area = $params['area'];
        $namespace = $params['namespace'];
        $module = $params['module'];
        if ($params['include_code']) {
            $locationPaths = [];
            foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
                $locationPaths[] = $moduleDir . "/view/{$area}/{$location}";
            }
            $this->_accumulateFilesByPatterns(
                $locationPaths,
                '*.xml',
                $files,
                $params['with_metainfo'] ? '_parseModuleLayout' : false
            );
        }
        if ($params['include_design']) {
            $locationPaths = [];
            foreach ($this->themePackageList->getThemes() as $theme) {
                if ($theme->getArea() === $area) {
                    $locationPaths[] = $theme->getPath() . "/{$namespace}_{$module}/{$location}";
                }
            }
            $this->_accumulateFilesByPatterns(
                $locationPaths,
                '*.xml',
                $files,
                $params['with_metainfo'] ? '_parseThemeLayout' : false
            );
        }
        self::$_cache[$method][$cacheKey] = $files;
    }

    /**
     * Parse meta-info of a layout file in module
     *
     * @param string $file
     * @return array
     */
    protected function _parseModuleLayout($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $modulePath) {
            if (preg_match(
                '/^' . preg_quote("{$modulePath}/", '/') . 'view\/([a-z]+)\/layout\/(.+)$/i',
                $file,
                $matches
            ) === 1
            ) {
                list(, $area, $filePath) = $matches;
                return [$area, '', $moduleName, $filePath, $file];
            }
        }
        return [];
    }

    /**
     * Parse meta-info of a layout file in theme
     *
     * @param string $file
     * @return array
     */
    protected function _parseThemeLayout($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themePath) {
            $appDesign = preg_quote("{$themePath}/", '/');
            $invariant = '/^' . $appDesign . '([a-z\d]+_[a-z\d]+)\/layout\/';

            if (preg_match($invariant . 'override\/base\/(.+)$/i', $file, $matches)) {
                list(, $area, $themeNS, $themeCode, $module, $filePath) = $matches;
                return [$area, $themeNS . '/' . $themeCode, $module, $filePath];
            }
            if (preg_match($invariant . 'override\/theme\/[a-z\d_]+\/[a-z\d_]+\/(.+)$/i', $file, $matches)) {
                list(, $area, $themeNS, $themeCode, $module, $filePath) = $matches;
                return [$area, $themeNS . '/' . $themeCode, $module, $filePath];
            }
            preg_match($invariant . '(.+)$/i', $file, $matches);
            list(, $area, $themeNS, $themeCode, $module, $filePath) = $matches;
            return [$area, $themeNS . '/' . $themeCode, $module, $filePath, $file];
        }
        return [];
    }

    /**
     * Returns list of page_type files, used by Magento application modules
     *
     * An incoming array can contain the following items
     * array (
     *     'namespace'      => 'namespace_name',
     *     'module'         => 'module_name',
     *     'area'           => 'area_name',
     *     'theme'          => 'theme_name',
     * )
     *
     * @param array $incomingParams
     * @param bool $asDataSet
     * @return array
     */
    public function getPageTypeFiles($incomingParams = [], $asDataSet = true)
    {
        $params = ['area' => '*', 'theme_path' => '*/*'];
        foreach (array_keys($params) as $key) {
            if (isset($incomingParams[$key])) {
                $params[$key] = $incomingParams[$key];
            }
        }
        $cacheKey = md5(BP . '|' . implode('|', $params));

        if (!isset(self::$_cache[__METHOD__][$cacheKey])) {
            $etcAreaPaths = [];
            foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
                $etcAreaPaths[] = $moduleDir . "/etc/{$params['area']}";
            }
            $files = self::getFiles(
                $etcAreaPaths,
                'page_types.xml'
            );

            self::$_cache[__METHOD__][$cacheKey] = $files;
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[__METHOD__][$cacheKey]);
        }
        return self::$_cache[__METHOD__][$cacheKey];
    }

    /**
     * Returns list of Javascript files in Magento
     *
     * @param string $area
     * @param string $themePath
     * @param string $namespace
     * @param string $module
     * @return array
     */
    public function getJsFiles($area = '*', $themePath = '*/*', $namespace = '*', $module = '*')
    {
        $key = $area . $themePath . $namespace . $module . __METHOD__ . BP;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $moduleWebPaths = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $moduleDir) {
            $keyInfo = explode('_', $moduleName);
            if ($keyInfo[0] == $namespace || $namespace == '*') {
                if ($keyInfo[1] == $module || $module == '*') {
                    $moduleWebPaths[] = $moduleDir . "/view/{$area}/web";
                }
            }
        }
        $themePaths = [];
        foreach ($this->themePackageList->getThemes() as $theme) {
            if ($theme->getArea() === $area) {
                $themePaths[] = $theme->getPath() . "/web";
                $themePaths[] = $theme->getPath() . "/{$module}/web";
            }
        }
        $files = self::getFiles(
            array_merge(
                [
                    BP . "/lib/web/{mage,varien}"
                ],
                $themePaths,
                $moduleWebPaths
            ),
            '*.js'
        );
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Returns list of Static HTML files in Magento
     *
     * @param string $area
     * @param string $themePath
     * @param string $namespace
     * @param string $module
     * @return array
     */
    public function getStaticHtmlFiles($area = '*', $themePath = '*/*', $namespace = '*', $module = '*')
    {
        $key = $area . $themePath . $namespace . $module . __METHOD__ . BP;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $moduleTemplatePaths = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $moduleDir) {
            $keyInfo = explode('_', $moduleName);
            if ($keyInfo[0] == $namespace || $namespace == '*') {
                if ($keyInfo[1] == $module || $module == '*') {
                    $moduleTemplatePaths[] = $moduleDir . "/view/{$area}/web/template";
                }
            }
        }
        $themePaths = [];
        foreach ($this->themePackageList->getThemes() as $theme) {
            if ($theme->getArea() === $area) {
                $themePaths[] = $theme->getPath() . "/web/template";
                $themePaths[] = $theme->getPath() . "/{$module}/web/template";
            }
        }
        $files = self::getFiles(
            array_merge(
                $themePaths,
                $moduleTemplatePaths
            ),
            '*.html'
        );
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Get list of static view files that are subject of Magento static view files pre-processing system
     *
     * @param string $filePattern
     * @return array
     */
    public function getStaticPreProcessingFiles($filePattern = '*')
    {
        $key = __METHOD__ . BP . '|' . $filePattern;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $module = '*';
        $area = '*';
        $themePath = '*/*';
        $locale = '*';
        $result = [];
        $moduleWebPath = [];
        $moduleLocalePath = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $moduleWebPath[] = $moduleDir . "/view/{$area}/web";
            $moduleLocalePath[] = $moduleDir . "/view/{$area}/web/i18n/{$locale}";
        }

        $themePaths = [];
        $themeLocalePath = [];
        foreach ($this->themePackageList->getThemes() as $theme) {
            if ($theme->getArea() === $area) {
                $themePaths[] = $theme->getPath() . "/web";
                $themePaths[] = $theme->getPath() . "/{$module}/web";
                $themeLocalePath[] = $theme->getPath() . "/web/i18n/{$locale}";
                $themeLocalePath[] = $theme->getPath() . "/{$module}/web/i18n/{$locale}";
            }
        }

        $this->_accumulateFilesByPatterns($moduleWebPath, $filePattern, $result, '_parseModuleStatic');

        $this->_accumulateFilesByPatterns($moduleLocalePath, $filePattern, $result, '_parseModuleLocaleStatic');
        $this->_accumulateFilesByPatterns($themePaths, $filePattern, $result, '_parseThemeStatic');
        $this->_accumulateFilesByPatterns($themeLocalePath, $filePattern, $result, '_parseThemeLocaleStatic');
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Get all files from static library directory
     *
     * @return array
     */
    public function getStaticLibraryFiles()
    {
        $result = [];
        $this->_accumulateFilesByPatterns([BP . "/lib/web"], '*', $result, '_parseLibStatic');
        return $result;
    }

    /**
     * Parse file path from the absolute path of static library
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    protected function _parseLibStatic($file, $path)
    {
        preg_match('/^' . preg_quote("{$path}/lib/web/", '/') . '(.+)$/i', $file, $matches);
        return $matches[1];
    }

    /**
     * Search files by the specified patterns and accumulate them, applying a callback to each found row
     *
     * @param array $patterns
     * @param string $filePattern
     * @param array $result
     * @param bool $subroutine
     * @return void
     */
    protected function _accumulateFilesByPatterns(array $patterns, $filePattern, array &$result, $subroutine = false)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', BP);
        foreach (self::getFiles($patterns, $filePattern) as $file) {
            $file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            if ($subroutine) {
                $result[] = $this->$subroutine($file, $path);
            } else {
                $result[] = $file;
            }
        }
    }

    /**
     * Parse meta-info of a static file in module
     *
     * @param string $file
     * @return array
     */
    protected function _parseModuleStatic($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $modulePath) {
            if (preg_match(
                '/^' . preg_quote("{$modulePath}/", '/') . 'view\/([a-z]+)\/web\/(.+)$/i',
                $file,
                $matches
            ) === 1
            ) {
                list(, $area, $filePath) = $matches;
                return [$area, '', '', $moduleName, $filePath, $file];
            }
        }
        return [];
    }

    /**
     * Parse meta-info of a localized (translated) static file in module
     *
     * @param string $file
     * @return array
     */
    protected function _parseModuleLocaleStatic($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $modulePath) {
            $appCode = preg_quote("{$modulePath}/", '/');
            if (preg_match('/^' . $appCode . 'view\/([a-z]+)\/web\/i18n\/([a-z_]+)\/(.+)$/i', $file, $matches) === 1) {
                list(, $area, $locale, $filePath) = $matches;
                return [$area, '', $locale, $moduleName, $filePath, $file];
            }
        }
        return [];
    }

    /**
     * Parse meta-info of a static file in theme
     *
     * @param string $file
     * @param string $path
     * @return array
     */
    protected function _parseThemeStatic($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themePath) {
            $appDesign = preg_quote("{$themePath}/", '/');
            if (preg_match(
                '/^' . $appDesign . '([a-z\d]+_[a-z\d]+)\/web\/(.+)$/i',
                $file,
                $matches
            )) {
                list(, $area, $themeNS, $themeCode, $module, $filePath) = $matches;
                return [$area, $themeNS . '/' . $themeCode, '', $module, $filePath, $file];
            }

            preg_match(
                '/^' . $appDesign . '\/web\/(.+)$/i',
                $file,
                $matches
            );
            list(, $area, $themeNS, $themeCode, $filePath) = $matches;
            return [$area, $themeNS . '/' . $themeCode, '', '', $filePath, $file];
        }
        return [];
    }

    /**
     * Parse meta-info of a localized (translated) static file in theme
     *
     * @param string $file
     * @return array
     */
    protected function _parseThemeLocaleStatic($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themePath) {
            $design = preg_quote("{$themePath}/", '/');
            if (preg_match(
                '/^' . $design . '([a-z\d]+_[a-z\d]+)\/web\/i18n\/([a-z_]+)\/(.+)$/i',
                $file,
                $matches
            )) {
                list(, $area, $themeNS, $themeCode, $module, $locale, $filePath) = $matches;
                return [$area, $themeNS . '/' . $themeCode, $locale, $module, $filePath, $file];
            }

            preg_match(
                '/^' . $design . '\/web\/i18n\/([a-z_]+)\/(.+)$/i',
                $file,
                $matches
            );
            list(, $area, $themeNS, $themeCode, $locale, $filePath) = $matches;
            return [$area, $themeNS . '/' . $themeCode, $locale, '', $filePath, $file];
        }
        return [];
    }

    /**
     * Returns list of Javascript files in Magento by certain area
     *
     * @param string $area
     * @return array
     */
    public function getJsFilesForArea($area)
    {
        $key = __METHOD__ . BP . $area;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $viewAreaPaths = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $viewAreaPaths[] = $moduleDir . "/view/{$area}";
        }
        $themePaths = [];
        foreach ($this->themePackageList->getThemes() as $theme) {
            if ($theme->getArea() === $area) {
                $themePaths[] = $theme->getPath();
            }
        }
        $paths = [
            BP . "/lib/web/varien"
        ];
        $paths = array_merge($paths, $viewAreaPaths, $themePaths);
        $files = self::getFiles($paths, '*.js');

        if ($area == 'adminhtml') {
            $adminhtmlPaths = [BP . "/lib/web/mage/{adminhtml,backend}"];
            $files = array_merge($files, self::getFiles($adminhtmlPaths, '*.js'));
        } else {
            $frontendPaths = [BP . "/lib/web/mage"];
            /* current structure of /lib/web/mage directory contains frontend javascript in the root,
               backend javascript in subdirectories. That's why script shouldn't go recursive throught subdirectories
               to get js files for frontend */
            $files = array_merge($files, self::getFiles($frontendPaths, '*.js', false));
        }

        self::$_cache[$key] = $files;
        return $files;
    }

    /**
     * Returns list of Phtml files in Magento app directory.
     *
     * @param bool $withMetaInfo
     * @param bool $asDataSet
     * @return array
     */
    public function getPhtmlFiles($withMetaInfo = false, $asDataSet = true)
    {
        $key = __METHOD__ . BP . '|' . (int)$withMetaInfo;
        if (!isset(self::$_cache[$key])) {
            $namespace = '*';
            $module = '*';
            $area = '*';
            $result = [];
            $moduleTemplatePaths = [];
            foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
                $moduleTemplatePaths[] = $moduleDir . "/view/{$area}/templates";
            }
            $this->_accumulateFilesByPatterns(
                $moduleTemplatePaths,
                '*.phtml',
                $result,
                $withMetaInfo ? '_parseModuleTemplate' : false
            );

            $themePaths = [];
            foreach ($this->themePackageList->getThemes() as $theme) {
                if ($theme->getArea() === $area) {
                    $themePaths[] = $theme->getPath();
                }
            }

            $this->_accumulateFilesByPatterns(
                $themePaths,
                '*.phtml',
                $result,
                $withMetaInfo ? '_parseThemeTemplate' : false
            );
            self::$_cache[$key] = $result;
        }
        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$key]);
        }
        return self::$_cache[$key];
    }

    /**
     * Parse meta-information from a modular template file
     *
     * @param string $file
     * @return array
     */
    protected function _parseModuleTemplate($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $modulePath) {
            if (preg_match(
                '/^' . preg_quote("{$modulePath}/", '/') . 'view\/([a-z]+)\/templates\/(.+)$/i',
                $file,
                $matches
            ) === 1
            ) {
                list(, $area, $filePath) = $matches;
                return [$area, '', $moduleName, $filePath, $file];
            }
        }
        return [];
    }

    /**
     * Parse meta-information from a theme template file
     *
     * @param string $file
     * @return array
     */
    protected function _parseThemeTemplate($file)
    {
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themePath) {
            $appDesign = preg_quote("{$themePath}/", '/');

            preg_match(
                '/^' . $appDesign . '([a-z\d]+_[a-z\d]+)\/templates\/(.+)$/i',
                $file,
                $matches
            );
            list(, $area, $themeNS, $themeCode, $module, $filePath) = $matches;
            return [$area, $themeNS . '/' . $themeCode, $module, $filePath, $file];
        }
        return [];
    }

    /**
     * Returns list of email template files
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        $key = __METHOD__ . BP;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $moduleEmailPaths = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $moduleEmailPaths[] = $moduleDir . "/view/email";
        }
        $files = self::getFiles($moduleEmailPaths, '*.html');
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Return list of all files. The list excludes tool-specific files
     * (e.g. Git, IDE) or temp files (e.g. in "var/").
     *
     * @return array
     */
    public function getAllFiles()
    {
        $key = __METHOD__ . BP;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }

        $subFiles = self::getFiles(
            [
                BP . '/app',
                BP . '/dev',
                BP . '/lib',
                BP . '/pub'
            ],
            '*'
        );

        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::LANGUAGE) as $fullLanguageDir) {
            $subFiles[] = $fullLanguageDir . '/';
        }
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themeDir) {
            $subFiles[] = $themeDir . '/';
        }
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $subFiles[] = $moduleDir . '/';
        }
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::LIBRARY) as $libDir) {
            $subFiles[] = $libDir . '/';
        }

        $subFiles = array_merge($subFiles, $this->getPaths());

        $rootFiles = glob(BP . '/*', GLOB_NOSORT);
        $rootFiles = array_filter(
            $rootFiles,
            function ($file) {
                return is_file($file);
            }
        );

        $result = array_merge($rootFiles, $subFiles);
        $result = self::composeDataSets($result);

        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Retrieve all files in folders and sub-folders that match pattern (glob syntax)
     *
     * @param array $dirPatterns
     * @param string $fileNamePattern
     * @param bool $recursive
     * @return array
     */
    public static function getFiles(array $dirPatterns, $fileNamePattern, $recursive = true)
    {
        $result = [];
        foreach ($dirPatterns as $oneDirPattern) {
            $oneDirPattern  = str_replace('\\', '/', $oneDirPattern);
            $entriesInDir = glob("{$oneDirPattern}/{$fileNamePattern}", GLOB_NOSORT | GLOB_BRACE);
            $subDirs = glob("{$oneDirPattern}/*", GLOB_ONLYDIR | GLOB_NOSORT | GLOB_BRACE);
            $filesInDir = array_diff($entriesInDir, $subDirs);

            if ($recursive) {
                $filesInSubDir = self::getFiles($subDirs, $fileNamePattern);
                $result = array_merge($result, $filesInDir, $filesInSubDir);
            }
        }
        return $result;
    }

    /**
     * Look for DI config through the system
     *
     * @param bool $asDataSet
     * @return array
     */
    public function getDiConfigs($asDataSet = false)
    {
        $primaryConfigs = glob(BP . '/app/etc/{di.xml,*/di.xml}', GLOB_BRACE);
        $moduleConfigs = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleDir) {
            $moduleConfigs = array_merge($moduleConfigs, glob($moduleDir . '/etc/{di,*/di}.xml', GLOB_BRACE));
        }
        $configs = array_merge($primaryConfigs, $moduleConfigs);

        if ($asDataSet) {
            $output = [];
            foreach ($configs as $file) {
                $output[$file] = [$file];
            }

            return $output;
        }
        return $configs;
    }

    /**
     * Get module and library paths
     *
     * @return array
     */
    private function getPaths()
    {
        $directories = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $fullModuleDir) {
            $directories[] = $fullModuleDir . '/';
        }
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::LIBRARY) as $libraryDir) {
            $directories[] = $libraryDir . '/';
        }
        return $directories;
    }

    /**
     * Check if specified class exists
     *
     * @param string $class
     * @param string &$path
     * @return bool
     */
    public function classFileExists($class, &$path = '')
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }
        $classParts = explode('\\', $class);
        $className = array_pop($classParts);
        $namespace = implode('\\', $classParts);
        $path = implode('/', explode('\\', $class)) . '.php';
        $directories = [
            '/dev/tools/',
            '/dev/tests/api-functional/framework/',
            '/dev/tests/integration/framework/',
            '/dev/tests/integration/framework/tests/unit/testsuite/',
            '/dev/tests/integration/testsuite/',
            '/dev/tests/integration/testsuite/Magento/Test/Integrity/',
            '/dev/tests/performance/framework/',
            '/dev/tests/static/framework/',
            '/dev/tests/static/testsuite/',
            '/dev/tests/functional/tests/app/',
            '/setup/src/'
        ];
        foreach ($directories as $key => $dir) {
            $directories[$key] = BP . $dir;
        }

        $directories = array_merge($directories, $this->getPaths());

        foreach ($directories as $dir) {
            $fullPath = $dir . $path;
            $trimmedFullPath = $dir . explode('/', $path, 3)[2];
            if ($this->classFileExistsCheckContent($fullPath, $namespace, $className)
                || $this->classFileExistsCheckContent($trimmedFullPath, $namespace, $className)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper function for classFileExists to check file content
     *
     * @param string $fullPath
     * @param string $namespace
     * @param string $className
     * @return bool
     */
    private function classFileExistsCheckContent($fullPath, $namespace, $className)
    {
        /**
         * Use realpath() instead of file_exists() to avoid incorrect work on Windows
         * because of case insensitivity of file names
         * Note that realpath() automatically changes directory separator to the OS-native
         * Since realpath won't work with symlinks we also check file_exists if realpath failed
         */
        if (realpath($fullPath) == str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath)
            || file_exists($fullPath)
        ) {
            $fileContent = file_get_contents($fullPath);
            if (strpos($fileContent, 'namespace ' . $namespace) !== false
                && (strpos($fileContent, 'class ' . $className) !== false
                    || strpos($fileContent, 'interface ' . $className) !== false)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return list of declared namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        $key = __METHOD__ . BP;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }

        $result = [];
        foreach (array_keys($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE)) as $moduleName) {
            $namespace = explode('_', $moduleName)[0];
            if (!in_array($namespace, $result) && $namespace !== 'Zend') {
                $result[] = $namespace;
            }
        }
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * @param string $namespace
     * @param string $module
     * @param string $file
     * @return string
     */
    public function getModuleFile($namespace, $module, $file)
    {
        return $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE)[$namespace . '_' . $module] .
        '/' . $file;
    }

    /**
     * Returns array of PHP-files for specified module
     *
     * @param string $module
     * @param bool $asDataSet
     * @return array
     */
    public function getModulePhpFiles($module, $asDataSet = true)
    {
        $key = __METHOD__ . "/{$module}";
        if (!isset(self::$_cache[$key])) {
            $files = self::getFiles(
                [$this->componentRegistrar->getPaths(ComponentRegistrar::MODULE)['Magento_'. $module]],
                '*.php'
            );
            self::$_cache[$key] = $files;
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$key]);
        }

        return self::$_cache[$key];
    }

    /**
     * Returns array of composer.json for specified app directory, such as code/Magento, design, i18n
     *
     * @param string $componentType
     * @param bool $asDataSet
     * @return array
     */
    public function getComposerFiles($componentType, $asDataSet = true)
    {
        $key = __METHOD__ . '|' . BP . '|' . serialize(func_get_args());
        if (!isset(self::$_cache[$key])) {
            $excludes = $componentType == ComponentRegistrar::MODULE ? $this->getModuleTestDirs() : [];
            $files = $this->getFilesSubset(
                $this->componentRegistrar->getPaths($componentType),
                'composer.json',
                $excludes
            );

            self::$_cache[$key] = $files;
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$key]);
        }

        return self::$_cache[$key];
    }

    /**
     * Read all text files by specified glob pattern and combine them into an array of valid files/directories
     *
     * The Magento root path is prepended to all (non-empty) entries
     *
     * @param string $globPattern
     * @return array
     * @throws \Exception if any of the patterns don't return any result
     */
    public static function readLists($globPattern)
    {
        $patterns = [];
        foreach (glob($globPattern) as $list) {
            $patterns = array_merge($patterns, file($list, FILE_IGNORE_NEW_LINES));
        }

        // Expand glob patterns
        $result = [];
        foreach ($patterns as $pattern) {
            if (0 === strpos($pattern, '#')) {
                continue;
            }
            /**
             * Note that glob() for directories will be returned as is,
             * but passing directory is supported by the tools (phpcpd, phpmd, phpcs)
             */
            $files = glob(self::init()->getPathToSource() . '/' . $pattern, GLOB_BRACE);
            if (empty($files)) {
                continue;
            }
            $result = array_merge($result, $files);
        }
        return $result;
    }

    /**
     * Check module existence
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModuleExists($moduleName)
    {
        $key = __METHOD__ . "/{$moduleName}";
        if (!isset(self::$_cache[$key])) {
            self::$_cache[$key] = file_exists(
                $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName)
            );
        }

        return self::$_cache[$key];
    }

    /**
     * Returns list of files in a given directory, minus files in specifically excluded directories.
     *
     * @param array $dirPatterns Directories to search in
     * @param string $fileNamePattern Pattern for filename
     * @param string|array $excludes Subdirectories to exlude, represented as regex
     * @return array Files in $dirPatterns but not in $excludes
     */
    protected function getFilesSubset(array $dirPatterns, $fileNamePattern, $excludes)
    {
        if (!is_array($excludes)) {
            $excludes = [$excludes];
        }
        $fileSet = self::getFiles($dirPatterns, $fileNamePattern);
        foreach ($excludes as $excludeRegex) {
            $fileSet = preg_grep($excludeRegex, $fileSet, PREG_GREP_INVERT);
        }
        return $fileSet;
    }
}
