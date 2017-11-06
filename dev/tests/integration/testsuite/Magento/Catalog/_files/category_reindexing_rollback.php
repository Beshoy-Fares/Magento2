<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Framework\Registry $registry */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$registry = $objectManager->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
/** @var $product \Magento\Catalog\Model\Product */
$product = $productRepository->get('simple', false, null, true);
if ($product->getId()) {
    $product->delete();
}

/** @var Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(Magento\Catalog\Api\CategoryRepositoryInterface::class);
$categoryIds = [3, 4, 5];
foreach ($categoryIds as $categoryId) {
    /** @var $category \Magento\Catalog\Model\Category */
    $category = $categoryRepository->get($categoryId);
    if ($category->getId()) {
        $category->delete();
    }
}
