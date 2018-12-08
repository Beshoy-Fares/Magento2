<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer;

class Website
{
    /**
     * @var TableMaintainer
     */
    private $tableMaintainer;

    /**
     * @param TableMaintainer $tableMaintainer
     */
    public function __construct(
        TableMaintainer $tableMaintainer
    ) {
        $this->tableMaintainer = $tableMaintainer;
    }

    /**
     * Delete catalog_category_product indexer tables for deleted website
     *
     * @param AbstractDb $subject
     * @param AbstractDb $objectResource
     * @param AbstractModel $website
     *
     * @return AbstractDb
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(AbstractDb $subject, AbstractDb $objectResource, AbstractModel $website)
    {
        foreach ($website->getStoreIds() as $storeId) {
<<<<<<< HEAD
            $this->tableMaintainer->dropTablesForStore($storeId);
=======
            $this->tableMaintainer->dropTablesForStore((int)$storeId);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        }
        return $objectResource;
    }
}
