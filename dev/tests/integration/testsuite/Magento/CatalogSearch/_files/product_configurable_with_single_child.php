<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->reinitialize();

require __DIR__ . '/../../../Magento/ConfigurableProduct/_files/configurable_attribute.php';

/** @var ProductRepositoryInterface $productRepository */
<<<<<<< HEAD
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);
=======
$productRepository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(CategorySetup::class);

/* Create simple products per each option value*/
/** @var AttributeOptionInterface[] $options */
$options = $attribute->getOptions();

$attributeValues = [];
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
<<<<<<< HEAD
$productsSku = [1410];
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
array_shift($options); //remove the first option which is empty

$option = reset($options);

/** @var $childProduct Product */
$childProduct = Bootstrap::getObjectManager()->create(Product::class);
<<<<<<< HEAD
$productSku = array_shift($productsSku);
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
$childProduct->setTypeId(Type::TYPE_SIMPLE)
    ->setAttributeSetId($attributeSetId)
    ->setName('Configurable Product Option' . $option->getLabel())
    ->setSku('configurable_option_single_child')
    ->setPrice(11)
    ->setTestConfigurable($option->getValue())
    ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
<<<<<<< HEAD
    ->setStatus(Status::STATUS_ENABLED);
$childProduct = $productRepository->save($childProduct);

/** @var StockItemInterface $stockItem */
$stockItem = $childProduct->getExtensionAttributes()->getStockItem();
$stockItem->setUseConfigManageStock(1)->setIsInStock(true)->setQty(100)->setIsQtyDecimal(0);

=======
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        ]
    );
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
$childProduct = $productRepository->save($childProduct);

$attributeValues[] = [
    'label' => 'test',
    'attribute_id' => $attribute->getId(),
    'value_index' => $option->getValue(),
];

/** @var $product Product */
$configurableProduct = Bootstrap::getObjectManager()->create(Product::class);

/** @var Factory $optionsFactory */
$optionsFactory = Bootstrap::getObjectManager()->create(Factory::class);

$configurableAttributesData = [
    [
        'attribute_id' => $attribute->getId(),
        'code' => $attribute->getAttributeCode(),
        'label' => $attribute->getStoreLabel(),
        'position' => '0',
        'values' => $attributeValues,
    ],
];

$configurableOptions = $optionsFactory->create($configurableAttributesData);

$extensionConfigurableAttributes = $configurableProduct->getExtensionAttributes();
$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
$extensionConfigurableAttributes->setConfigurableProductLinks([$childProduct->getId()]);

$configurableProduct->setExtensionAttributes($extensionConfigurableAttributes);

$configurableProduct->setTypeId(Configurable::TYPE_CODE)
    ->setAttributeSetId($attributeSetId)
    ->setName('Configurable Product with single child')
    ->setSku('configurable_with_single_child')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
<<<<<<< HEAD
    ->setStatus(Status::STATUS_ENABLED);
$configurableProduct = $productRepository->save($configurableProduct);

/** @var StockItemInterface $stockItem */
$stockItem = $configurableProduct->getExtensionAttributes()->getStockItem();
$stockItem->setUseConfigManageStock(1)->setIsInStock(1);

$productRepository->save($configurableProduct);
=======
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock'   => 1,
            'is_in_stock'               => 1,
        ]
    );
$configurableProduct = $productRepository->save($configurableProduct);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
