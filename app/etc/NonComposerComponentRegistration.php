<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * A helper class.
 * Goes through non-composer components under:
 * 1. app/code
 * 2. app/design
 * 3. app/i18n and
 * 4. lib/internal/Magento/Framework
 * looking for their registration.php files and executes them to get these components registered with Magento
 * framework.
 */

class NonComposerComponentRegistration
{
    /**
     * Instance of NonComposerComponentRegistration class
     *
     * @var NonComposerComponentRegistration instance
     */
    private static $instance = null;

     /**
     * The list of supported non-composer component paths
     *
     * @var string[] $pathList
     */
    private static $pathList;

    /**
     * private constructor.
     */
    private function __construct()
    {
        // The supported directories are:
        // 1. app/code
        // 2. app/design
        // 3. app/i18n
        // 4. lib/internal
        static::$pathList[] = dirname(__DIR__) . '/code/*/*/registration.php';
        static::$pathList[] = dirname(__DIR__) . '/design/*/*/*/registration.php';
        static::$pathList[] = dirname(__DIR__) . '/i18n/*/*/registration.php';
        static::$pathList[] = dirname(dirname(__DIR__)) . '/lib/internal/*/*/registration.php';
        static::$pathList[] = dirname(dirname(__DIR__)) . '/lib/internal/*/*/*/registration.php';
    }

    /**
     * Public static method to access the class instance
     *
     * @return NonComposerComponentRegistration
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new NonComposerComponentRegistration();
        }
        return static::$instance;
    }

    /**
     * Find the registration files and execute them. So that, these non-composer installed
     * components are registered within the framework.
     *
     * @return void
     */
    public function register()
    {
        $files = [];
        $staticList = __DIR__ . '/paths.php';
        if (file_exists($staticList)) {
            $files = include $staticList;
        } else {
            foreach (static::$pathList as $path) {
                // Sorting is disabled intentionally for performance improvement
                $files = array_merge($files, glob($path, GLOB_NOSORT));
                if ($files === false) {
                    throw new \RuntimeException('glob() returned error while searching in \'' . $path . '\'');
                }
            }

            $content = "<?php\n " .
                "/**\n" .
                "* Paths to registration files\n" .
                "*/\n" .
                "return [\n\t'" .
                implode("',\n\t'", $files) .
                "'\n];\n";
            file_put_contents($staticList, $content);
        }
        foreach ($files as $file) {
            include $file;
        }
    }
}

// Register all the non-composer components.
NonComposerComponentRegistration::getInstance()->register();
?>
