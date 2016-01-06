<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Communication\Config;

use Magento\Framework\Config\ReaderInterface;

/**
 * Composite reader for communication config.
 */
class CompositeReader implements ReaderInterface
{
    /**
     * ReaderInterface[]
     */
    private $readers;

    /**
     * Initialize dependencies.
     *
     * @param array $readersList
     */
    public function __construct(array $readersList)
    {
        $this->readers = $this->sortReaders($readersList);
    }

    /**
     * Read config.
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $result = [];
        foreach ($this->readers as $reader) {
            $result = array_replace_recursive($result, $reader->read($scope));
        }
        return $result;
    }

    /**
     * Sort readers.
     *
     * @param array $readersList
     * @return ReaderInterface[]
     */
    private function sortReaders(array $readersList)
    {
        usort(
            $readersList,
            function ($firstItem, $secondItem) {
                if (!isset($firstItem['sortOrder'])) {
                    return 1;
                }
                if (!isset($secondItem['sortOrder'])) {
                    return -1;
                }
                if ($firstItem['sortOrder'] == $secondItem['sortOrder']) {
                    return 0;
                }
                return $firstItem['sortOrder'] < $secondItem['sortOrder'] ? -1 : 1;
            }
        );
        $readers = [];
        foreach ($readersList as $readerInfo) {
            if (!isset($readerInfo['reader'])) {
                continue;
            }
            $readers[] = $readerInfo['reader'];
        }
        return $readers;
    }
}
