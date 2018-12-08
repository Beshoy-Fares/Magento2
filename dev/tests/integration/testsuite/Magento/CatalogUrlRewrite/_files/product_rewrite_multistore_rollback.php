<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
=======

declare(strict_types=1);

use Magento\Framework\Exception\NoSuchEntityException;

\Magento\TestFramework\Helper\Bootstrap::getInstance()->getInstance()->reinitialize();

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('product1', true);
    if ($product->getId()) {
        $productRepository->delete($product);
    }
} catch (NoSuchEntityException $e) {
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

require __DIR__ . '/../../Store/_files/store_rollback.php';
require __DIR__ . '/../../Store/_files/second_store_rollback.php';
