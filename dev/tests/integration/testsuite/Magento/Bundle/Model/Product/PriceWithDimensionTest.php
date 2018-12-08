<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Bundle\Model\Product;

/**
 * @magentoDbIsolation disabled
 * @magentoIndexerDimensionMode catalog_product_price website_and_customer_group
 * @group indexer_dimension
 * @magentoDataFixture Magento/Bundle/_files/product_with_tier_pricing.php
 */
class PriceWithDimensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Bundle\Model\Product\Price
     */
    protected $_model;

<<<<<<< HEAD
=======
    /**
     * Set up
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Bundle\Model\Product\Price::class
        );
    }

<<<<<<< HEAD
=======
    /**
     * Get tier price
     */
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    public function testGetTierPrice()
    {
        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $productRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $product = $productRepository->get('bundle-product');
        // fixture

        // Note that this is really not the "tier price" but the "tier discount percentage"
        // so it is expected to be increasing instead of decreasing
        $this->assertEquals(8.0, $this->_model->getTierPrice(2, $product));
        $this->assertEquals(20.0, $this->_model->getTierPrice(3, $product));
        $this->assertEquals(20.0, $this->_model->getTierPrice(4, $product));
        $this->assertEquals(30.0, $this->_model->getTierPrice(5, $product));
    }
}
