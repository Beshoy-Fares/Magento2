<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test to ensure that readme file present in specified directories
 */
namespace Magento\Test\Integrity\Readme;

use Magento\Framework\App\Utility\Files;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    const README_FILENAME = 'README.md';

    const BLACKLIST_FILES_PATTERN = '_files/blacklist/*.txt';

    const SCAN_LIST_FILE = '_files/scan_list.txt';

    /** @var array Blacklisted files and directories */
    private $blacklist = [];

    /** @var array */
    private $scanList = [];

    protected function setUp()
    {
        $this->blacklist = Files::init()->readLists(__DIR__ . DIRECTORY_SEPARATOR . self::BLACKLIST_FILES_PATTERN);
        array_walk($this->blacklist, function (&$item) {
            $item = rtrim($item, '/');
        });
        $this->scanList = Files::init()->readLists(__DIR__ . '/' . self::SCAN_LIST_FILE);
        array_walk($this->scanList, function (&$item) {
            $item = rtrim($item, '/');
        });
    }

    public function testReadmeFiles()
    {
        $invoker = new \Magento\Framework\App\Utility\AggregateInvoker($this);
        $invoker(
        /**
         * @param string $dir
         */
            function ($dir) {
                $file = $dir . DIRECTORY_SEPARATOR . self::README_FILENAME;
                $this->assertFileExists(
                    $file,
                    sprintf('File %s not found in %s', self::README_FILENAME, $dir)
                );
            },
            $this->getDirectories()
        );
    }

    /**
     * @return array
     */
    private function getDirectories()
    {
        $directories = [];
        foreach ($this->scanList as $dir) {
            if (!$this->isInBlacklist($dir)) {
                $directories[][$dir] = $dir;
            }
        }

        return $directories;
    }

    /**
     * @param $path
     * @return bool
     */
    private function isInBlacklist($path)
    {
        return in_array($path, $this->blacklist);
    }
}
