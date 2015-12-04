<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Design;

use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Theme\Model\Design\Config\MetadataProvider;
use Magento\Theme\Model\ResourceModel\Design\Config\CollectionFactory;

class BackendModelFactory extends ValueFactory
{
    /**
     * @var array
     */
    protected $storedData = [];

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param MetadataProvider $metadataProvider
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        MetadataProvider $metadataProvider,
        CollectionFactory $collectionFactory
    ) {
        $this->metadataProvider = $metadataProvider;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($objectManager);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data = [])
    {
        $backendModelData = [
            'path' => $data['config']['path'],
            'scope' => $data['scope'],
            'scope_id' => $data['scopeId'],
            'field_config' => $data['config'],
        ];
        $configId = $this->getConfigId($data['scope'], $data['scopeId'], $data['config']['path']);
        if ($configId) {
            $backendModelData['config_id'] = $configId;
        }

        $backendModel = isset($data['config']['backend_model'])
            ? $this->_objectManager->create($data['config']['backend_model'], ['data' => $backendModelData])
            : parent::create(['data' => $backendModelData]);
        $backendModel->setValue($data['value']);

        return $backendModel;
    }

    /**
     * Get config id for path
     *
     * @param string $scope
     * @param string $scopeId
     * @param string $path
     * @return null
     */
    protected function getConfigId($scope, $scopeId, $path)
    {
        $storedData = $this->getStoredData($scope, $scopeId);
        $dataKey = array_search($path, array_column($storedData, 'path'));
        return isset($storedData[$dataKey]['config_id']) ? $storedData[$dataKey]['config_id'] : null;
    }

    /**
     * Get stored data for scope and scope id
     *
     * @param string $scope
     * @param string $scopeId
     * @return array
     */
    protected function getStoredData($scope, $scopeId)
    {
        if (!isset($this->storedData[$scope][$scopeId])) {
            $collection = $this->collectionFactory->create();
            $collection->addPathsFilter($this->getMetadata());
            $collection->addFieldToFilter('scope', $scope);
            $collection->addScopeIdFilter($scopeId);
            $this->storedData[$scope][$scopeId] = $collection->getData();
        }
        return $this->storedData[$scope][$scopeId];
    }

    /**
     * Retrieve metadata
     *
     * @return array
     */
    protected function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = $this->metadataProvider->get();
            array_walk($this->metadata, function (&$value) {
                $value = $value['path'];
            });
        }
        return $this->metadata;
    }
}
