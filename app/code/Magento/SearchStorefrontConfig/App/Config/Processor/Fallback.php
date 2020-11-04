<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SearchStorefrontConfig\App\Config\Processor;

use Magento\Framework\App\Config\Spi\PostProcessorInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\TableNotFoundException;
use Magento\Store\App\Config\Type\Scopes;

/**
 * Fallback through different scopes and merge them
 */
class Fallback implements PostProcessorInterface
{
    const STORE_TABLE = 'store';
    const WEBSITE_TABLE = 'store_website';

    /**
     * @var Scopes
     */
    private $scopes;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $storeData = [];

    /**
     * @var array
     */
    private $websiteData = [];

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * Fallback constructor.
     *
     * @param Scopes $scopes
     * @param ResourceConnection $resourceConnection
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        Scopes $scopes,
        ResourceConnection $resourceConnection,
        DeploymentConfig $deploymentConfig
    ) {
        $this->scopes = $scopes;
        $this->resourceConnection = $resourceConnection;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @inheritdoc
     */
    public function process(array $data)
    {
        $this->loadScopes();

        $defaultConfig = isset($data['default']) ? $data['default'] : [];
        $result = [
            'default' => $defaultConfig,
            'websites' => [],
            'stores' => []
        ];

        $websitesConfig = isset($data['websites']) ? $data['websites'] : [];
        $result['websites'] = $this->prepareWebsitesConfig($defaultConfig, $websitesConfig);

        $storesConfig = isset($data['stores']) ? $data['stores'] : [];
        $result['stores'] = $this->prepareStoresConfig($defaultConfig, $websitesConfig, $storesConfig);

        return $result;
    }

    /**
     * Prepare website data from Config/Type/Scopes
     *
     * @param array $defaultConfig
     * @param array $websitesConfig
     * @return array
     */
    private function prepareWebsitesConfig(
        array $defaultConfig,
        array $websitesConfig
    ) {
        $result = [];
        foreach ((array)$this->websiteData as $website) {
            $code = $website['code'];
            $id = $website['website_id'];
            $websiteConfig = isset($websitesConfig[$code]) ? $websitesConfig[$code] : [];
            $result[$code] = array_replace_recursive($defaultConfig, $websiteConfig);
            $result[$id] = $result[$code];
        }
        return $result;
    }

    /**
     * Prepare stores data from Config/Type/Scopes
     *
     * @param array $defaultConfig
     * @param array $websitesConfig
     * @param array $storesConfig
     * @return array
     */
    private function prepareStoresConfig(
        array $defaultConfig,
        array $websitesConfig,
        array $storesConfig
    ) {
        $result = [];

        foreach ((array)$this->storeData as $store) {
            $code = $store['code'];
            $id = $store['store_id'];
            $websiteConfig = [];
            if (isset($store['website_id'])) {
                $websiteConfig = $this->getWebsiteConfig($websitesConfig, $store['website_id']);
            }
            $storeConfig = isset($storesConfig[$code]) ? $storesConfig[$code] : [];
            $result[$code] = array_replace_recursive($defaultConfig, $websiteConfig, $storeConfig);
            $result[$id] = $result[$code];
        }
        return $result;
    }

    /**
     * Find information about website by its ID.
     *
     * @param array $websites Has next format: (website_code => [website_data])
     * @param int $id
     * @return array
     */
    private function getWebsiteConfig(array $websites, $id)
    {
        foreach ((array)$this->websiteData as $website) {
            if ($website['website_id'] == $id) {
                $code = $website['code'];
                return $websites[$code] ?? [];
            }
        }
        return [];
    }

    /**
     * Load config from database.
     *
     * @return void
     */
    private function loadScopes(): void
    {
        try {
            if ($this->deploymentConfig->isDbAvailable()) {
                $this->storeData = $this->readAllStores();
                $this->websiteData = $this->readAllWebsites();
            } else {
                $this->storeData = $this->scopes->get('stores');
                $this->websiteData = $this->scopes->get('websites');
            }
        } catch (TableNotFoundException $exception) {
            // database is empty or not setup
            $this->storeData = [];
            $this->websiteData = [];
        }
    }

    /**
     * Read all stores from DB.
     *
     * @return array
     */
    public function readAllStores()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(self::STORE_TABLE));
        return $connection->fetchAll($select);
    }

    /**
     * Read all websites from DB.
     *
     * @return array
     */
    public function readAllWebsites()
    {
        $websites = [];
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(self::WEBSITE_TABLE));

        foreach ($connection->fetchAll($select) as $websiteData) {
            $websites[$websiteData['code']] = $websiteData;
        }

        return $websites;
    }
}
