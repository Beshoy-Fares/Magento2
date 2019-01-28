<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\TestCase\Product;


/**
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create simple product.
 * 4. Fill in data according to data set.
 * 5. Save Product.
 * 6. Perform appropriate assertions.
 *
 * @group Products
 * @ZephyrId MAGETWO-23414, MAGETWO-17475, MAGETWO-43376
 */
class CreateSimpleProductEntityPartTwoTest extends CreateSimpleProductEntityTest
{
    /* tags */
    const TEST_TYPE = 'acceptance_test, extended_acceptance_test';
    const MVP = 'yes';
    /* end tags */

    // This blank class is created only to run long variation as a separate test in parallel environment
}
