<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Catalog\Model\Category\Product;

/**
 * Resolver to get product positions by ids assigned to specific category
 */
class PositionResolver extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
<<<<<<< HEAD
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * Get category product positions
     *
     * @param int $categoryId
     * @return array
     */
<<<<<<< HEAD
    public function getPositions(int $categoryId)
=======
    public function getPositions(int $categoryId): array
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            ['cpe' => $this->getTable('catalog_product_entity')],
            'entity_id'
        )->joinLeft(
            ['ccp' => $this->getTable('catalog_category_product')],
            'ccp.product_id=cpe.entity_id'
        )->where(
            'ccp.category_id = ?',
            $categoryId
        )->order(
            'ccp.position ' . \Magento\Framework\DB\Select::SQL_ASC
<<<<<<< HEAD
=======
        )->order(
            'ccp.product_id ' . \Magento\Framework\DB\Select::SQL_DESC
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        );

        return array_flip($connection->fetchCol($select));
    }
}
