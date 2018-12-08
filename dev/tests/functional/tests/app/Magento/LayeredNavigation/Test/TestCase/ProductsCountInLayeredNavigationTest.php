<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\LayeredNavigation\Test\TestCase;

use Magento\Catalog\Test\Fixture\Category;
use Magento\Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Preconditions:
<<<<<<< HEAD
 * 1. Create four categories assigned in ascending order (Default Category->first->second->third->fourth)
 * first and third categories should not be anchored, second and fourth categories should be anchored
 * 2. Create configurable product with two configurable options and assign it to category "fourth"
 *
 * Steps:
 * 1. Disable configurable options via massaction or from edit product page
 * 2. Open created non anchored categories on frontend
 * 3. Perform assertions
 *
 * @group Layered_Navigation
 * @ZephyrId MAGETWO-82891
=======
 * 1. Create four categories assigned in ascending order:
 *  - Category 1:
 *      Category name: "Test1"
 *      Parent Category: Default Category.
 *      Display settings -> Is Anchor: No
 *  - Category 2:
 *      Category name: "Test2"
 *      Parent Category: Test1
 *      Display settings -> Is Anchor: Yes
 *  - Category 3:
 *      Category name: "Test3"
 *      Parent Category: Test2
 *      Display settings -> Is Anchor: No
 *  - Category 4:
 *      Category name: "Test4"
 *      Parent Category: Test3
 *      Display settings -> Is Anchor: Yes
 * 2. Create configurable product with two configurable options and assign it to category "Test4"
 *
 * Steps:
 * 1. Disable configurable options via massaction or from edit product page
 * 2. Open created non anchored categories on frontend.
 *    On the left side in the Layered Navigation there is one category inside each of achored with zero products inside.
 * 3. Open created anchored categories on frontend. There are no products inside.
 *
 * @group Layered_Navigation
 * @ZephyrId MAGETWO-90123
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class ProductsCountInLayeredNavigationTest extends Injectable
{
    /**
     * Product page with a grid
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Page to update a product
     *
     * @var CatalogProductEdit
     */
    protected $editProductPage;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $editProductPage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $editProductPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->editProductPage = $editProductPage;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
<<<<<<< HEAD
     * Test category name and products count displaying in layered navigation after configurable options disabling
     *
     * @param Category $category
     * @param boolean $disableFromProductsGreed
=======
     * Test category name and products count displaying in layered navigation after configurable options disabling.
     *
     * @param Category $category
     * @param bool $disableFromProductsGreed
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @return array
     */
    public function test(
        Category $category,
<<<<<<< HEAD
        $disableFromProductsGreed = true
=======
        bool $disableFromProductsGreed = true
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    ) {
        // Preconditions
        $category->persist();
        // Steps
        $products = $category->getDataFieldConfig('category_products')['source']->getProducts();
        $configurableOptions = [];
        /** @var \Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct\ $product */
        foreach ($products as $product) {
            $configurableOptions = array_merge(
                $configurableOptions,
                $product->getConfigurableAttributesData()['matrix']
            );
        }
        // Disable configurable options
        if ($disableFromProductsGreed) {
            $this->catalogProductIndex->open();
            $this->catalogProductIndex->getProductGrid()->massaction(
                array_map(
                    function ($assignedProduct) {
                        return ['sku' => $assignedProduct['sku']];
                    },
                    $configurableOptions
                ),
                ['Change status' => 'Disable']
            );
        } else {
            $productToDisable = $this->fixtureFactory->createByCode(
                'catalogProductSimple',
                ['data' => ['status' => 'No']]
            );
            foreach ($configurableOptions as $configurableOption) {
                $filter = ['sku' => $configurableOption['sku']];
                $this->catalogProductIndex->open();
                $this->catalogProductIndex->getProductGrid()->searchAndOpen($filter);
                $this->editProductPage->getProductForm()->fill($productToDisable);
                $this->editProductPage->getFormPageActions()->save();
            }
        }
<<<<<<< HEAD
        return [
            'products' => $configurableOptions
=======

        return [
            'products' => $configurableOptions,
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        ];
    }
}
