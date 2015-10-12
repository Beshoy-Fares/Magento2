<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

            $attributeGroupId = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Images');

            // update General Group
            $categorySetup->updateAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'attribute_group_name',
                'Images and Videos'
            );
            $select = $setup->getConnection()->select()
                ->from(
                    $setup->getTable('catalog_product_entity_group_price'),
                    [
                        'value_id',
                        'entity_id',
                        'all_groups',
                        'customer_group_id',
                        new \Zend_Db_Expr('1'),
                        'value',
                        'website_id'
                    ]
                );
            $setup->getConnection()->insertFromSelect(
                $select,
                $setup->getTable('catalog_product_entity_group_price'),
                [
                    'value_id',
                    'entity_id',
                    'all_groups',
                    'customer_group_id',
                    'qty',
                    'value',
                    'website_id'
                ]
            );
            $categorySetupManager = $this->categorySetupFactory->create();
            $categorySetupManager->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'group_price');
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Category::ENTITY,
                'entity_model',
                'Magento\Catalog\Model\ResourceModel\Category'
            );
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Category::ENTITY,
                'attribute_model',
                'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
            );
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Category::ENTITY,
                'entity_attribute_collection',
                'Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection'
            );
            $categorySetup->updateAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'custom_design_from',
                'attribute_model',
                'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
            );
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Product::ENTITY,
                'entity_model',
                'Magento\Catalog\Model\ResourceModel\Product'
            );
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Product::ENTITY,
                'attribute_model',
                'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
            );
            $categorySetup->updateEntityType(
                \Magento\Catalog\Model\Product::ENTITY,
                'entity_attribute_collection',
                'Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection'
            );
        }
        $setup->endSetup();
    }
}
