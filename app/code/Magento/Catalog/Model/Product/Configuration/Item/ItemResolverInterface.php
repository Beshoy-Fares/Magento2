<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product\Configuration\Item;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Resolves the product from a configured item.
 *
 * @api
 */
interface ItemResolverInterface
{
    /**
     * Get the final product from a configured item by product type and selection.
     *
     * @param ItemInterface $item
     * @return ProductInterface
     */
<<<<<<< HEAD
    public function getFinalProduct(ItemInterface $item): ProductInterface;
=======
    public function getFinalProduct(ItemInterface $item) : ProductInterface;
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
}
