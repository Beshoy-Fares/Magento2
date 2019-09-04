<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\ImportExport\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

/**
 * Data provider for export grid.
 */
class ExportFileDataProvider extends DataProvider
{
    /**
     * @var DriverInterface
     */
    private $file;

    /**
     * @var Filesystem
     */
    private $fileSystem;
    
    /**
     * @var File
     */
    private $fileio;
    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param DriverInterface $file
     * @param Filesystem $filesystem
     * @param File $fileIO
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        DriverInterface $file,
        Filesystem $filesystem,
        File $fileIO,
        array $meta = [],
        array $data = []
    ) {
        $this->file = $file;
        $this->fileSystem = $filesystem;
        $this->fileio = $fileIO;
        $this->request = $request;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Returns data for grid.
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getData()
    {
        $directory = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $emptyResponse = ['items' => [], 'totalRecords' => 0];
        if (!$this->file->isExists($directory->getAbsolutePath() . 'export/')) {
            return $emptyResponse;
        }

        $files = $this->file->readDirectoryRecursively($directory->getAbsolutePath() . 'export/');
        if (empty($files)) {
            return $emptyResponse;
        }
        $result = [];
        foreach ($files as $file) {
            $result['items'][]['file_name'] = $this->fileio->getPathInfo($file)['basename'];
        }

        $pagesize = (int)($this->request->getParam('paging')['pageSize']);
        $pageCurrent = (int)($this->request->getParam('paging')['current']);
        $pageoffset = ($pageCurrent - 1) * $pagesize;

        $result['totalRecords'] = count($result['items']);
        $result['items'] = array_slice($result['items'],$pageoffset,$pagesize);

        return $result;
    }
}
