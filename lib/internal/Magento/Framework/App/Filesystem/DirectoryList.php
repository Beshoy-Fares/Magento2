<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Filesystem;

use Magento\Framework\Code\Generator\Io;

/**
 * A Magento application specific list of directories
 */
class DirectoryList extends \Magento\Framework\Filesystem\DirectoryList
{
    /**
     * Code base root
     */
    const ROOT = 'base';

    /**
     * Most of entire application
     */
    const APP = 'app';

    /**
     * Initial configuration of the application
     */
    const CONFIG = 'etc';

    /**
     * Libraries or third-party components
     */
    const LIB_INTERNAL = 'lib_internal';

    /**
     * Libraries/components that need to be accessible publicly through web-server (such as various DHTML components)
     */
    const LIB_WEB = 'lib_web';

    /**
     * \Directory within document root of a web-server to access static view files publicly
     */
    const PUB = 'pub';

    /**
     * Storage of files entered or generated by the end-user
     */
    const MEDIA = 'media';

    /**
     * Storage of static view files that are needed on HTML-pages, emails or similar content
     */
    const STATIC_VIEW = 'static';

    /**
     * Various files generated by the system in runtime
     */
    const VAR_DIR = 'var';

    /**
     * Temporary files
     */
    const TMP = 'tmp';

    /**
     * File system caching directory (if file system caching is used)
     */
    const CACHE = 'cache';

    /**
     * Logs of system messages and errors
     */
    const LOG = 'log';

    /**
     * File system session directory (if file system session storage is used)
     */
    const SESSION = 'session';

    /**
     * Directory for Setup application
     */
    const SETUP = 'setup';

    /**
     * Dependency injection related file directory
     *
     * @deprecated this constant become unused after moving folder for generated DI configuration files
     * to generated/metadata
     * @see self::GENERATED_METADATA
     */
    const DI = 'di';

    /**
     * Relative directory key for generated code
     *
     * @deprecated this constant become unused after moving folder for generated files to generated/code
     * @see self::GENERATED_CODE
     */
    const GENERATION = 'generation';

    /**
     * Temporary directory for uploading files by end-user
     */
    const UPLOAD = 'upload';

    /**
     * Directory to store composer related files (config, cache etc.) in case if composer runs by Magento Application
     */
    const COMPOSER_HOME = 'composer_home';

    /**
     * A suffix for temporary materialization directory where pre-processed files will be written (if necessary)
     */
    const TMP_MATERIALIZATION_DIR = 'view_preprocessed';

    /**
     * A suffix for temporary materialization directory where minified templates will be written (if necessary)
     * @deprecated since 2.2.0
     */
    const TEMPLATE_MINIFICATION_DIR = 'html';

    /**
     * Directory name for generated data.
     */
    const GENERATED = 'generated';

    /**
     * Relative directory key for generated code
     */
    const GENERATED_CODE = 'code';

    /**
     * Relative directory key for generated metadata
     */
    const GENERATED_METADATA = 'metadata';

    /**
     * Relative directory key for generated minified phtml sources
     */
    const GENERATED_MINIFIEDPHTML = 'minified_phtml';

    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig()
    {
        $result = [
            self::ROOT => [parent::PATH => ''],
            self::APP => [parent::PATH => 'app'],
            self::CONFIG => [parent::PATH => 'app/etc'],
            self::LIB_INTERNAL => [parent::PATH => 'lib/internal'],
            self::VAR_DIR => [parent::PATH => 'var'],
            self::CACHE => [parent::PATH => 'var/cache'],
            self::LOG => [parent::PATH => 'var/log'],
            self::DI => [parent::PATH => 'generated/metadata'],
            self::GENERATION => [parent::PATH => Io::DEFAULT_DIRECTORY],
            self::SESSION => [parent::PATH => 'var/session'],
            self::MEDIA => [parent::PATH => 'pub/media', parent::URL_PATH => 'pub/media'],
            self::STATIC_VIEW => [parent::PATH => 'pub/static', parent::URL_PATH => 'pub/static'],
            self::PUB => [parent::PATH => 'pub', parent::URL_PATH => 'pub'],
            self::LIB_WEB => [parent::PATH => 'lib/web'],
            self::TMP => [parent::PATH => 'var/tmp'],
            self::UPLOAD => [parent::PATH => 'pub/media/upload', parent::URL_PATH => 'pub/media/upload'],
            self::TMP_MATERIALIZATION_DIR => [parent::PATH => 'var/view_preprocessed/pub/static'],
            self::TEMPLATE_MINIFICATION_DIR => [parent::PATH => 'var/view_preprocessed'],
            self::SETUP => [parent::PATH => 'setup/src'],
            self::COMPOSER_HOME => [parent::PATH => 'var/composer_home'],
            self::GENERATED => [parent::PATH => 'generated'],
            self::GENERATED_CODE => [parent::PATH => Io::DEFAULT_DIRECTORY],
            self::GENERATED_METADATA => [parent::PATH => 'generated/metadata'],
            self::GENERATED_MINIFIEDPHTML => [parent::PATH => 'generated/minified_phtml']
        ];
        return parent::getDefaultConfig() + $result;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct($root, array $config = [])
    {
        parent::__construct($root, [self::ROOT => [self::PATH => $root]] + $config);
    }
}
