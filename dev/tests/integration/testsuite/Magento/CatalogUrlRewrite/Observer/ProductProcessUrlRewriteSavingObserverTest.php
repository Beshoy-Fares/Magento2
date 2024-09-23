<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogUrlRewrite\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Test\Fixture\Category as CategoryFixture;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Test\Fixture\Group as GroupFixture;
use Magento\Store\Test\Fixture\Store as StoreFixture;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureBeforeTransaction;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 * @magentoDbIsolation disabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductProcessUrlRewriteSavingObserverTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var DataFixtureStorage
     */
    private $fixtures;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->urlPersist = $this->objectManager->get(UrlPersistInterface::class);
        $this->fixtures = $this->objectManager->get(DataFixtureStorageManager::class)->getStorage();
    }

    /**
     * @param array $filter
     * @return array
     */
    private function getActualResults(array $filter)
    {
        /** @var UrlFinderInterface $urlFinder */
        $urlFinder = $this->objectManager->get(UrlFinderInterface::class);
        $actualResults = [];
        foreach ($urlFinder->findAllByData($filter) as $url) {
            $actualResults[$url->getRequestPath() . '-' . $url->getStoreId()] = [
                'request_path' => $url->getRequestPath(),
                'target_path' => $url->getTargetPath(),
                'is_auto_generated' => (int)$url->getIsAutogenerated(),
                'redirect_type' => $url->getRedirectType(),
                'store_id' => $url->getStoreId()
            ];
        }
        return $actualResults;
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @magentoAppIsolation enabled
     */
    public function testUrlKeyHasChangedInGlobalContext()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
        ];

        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContainsEquals($row, $actual);
        }

        $product->setData('save_rewrites_history', true);
        $product->setUrlKey('new-url');
        $product->setUrlPath('new-path');
        $this->productRepository->save($product);

        $expected = [
            [
                'request_path' => "new-url.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "new-url.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "new-url.html",
                'is_auto_generated' => 0,
                'redirect_type' => 301,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "new-url.html",
                'is_auto_generated' => 0,
                'redirect_type' => 301,
                'store_id' => $testStore4->getId(),
            ],
        ];

        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContainsEquals($row, $actual);
        }
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @magentoAppIsolation enabled
     */
    public function testUrlKeyHasChangedInStoreviewContextWithPermanentRedirection()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore($testStore1);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
        ];

        $product->setData('save_rewrites_history', true);
        $product->setUrlKey('new-url');
        $product->setUrlPath('new-path');
        $this->productRepository->save($product);

        $expected = [
            [
                'request_path' => "new-url.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "new-url.html",
                'is_auto_generated' => 0,
                'redirect_type' => 301,
                'store_id' => $testStore1->getId(),
            ],
        ];

        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContainsEquals($row, $actual);
        }
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @magentoAppIsolation enabled
     */
    public function testUrlKeyHasChangedInStoreviewContextWithoutPermanentRedirection()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore(1);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
        ];

        $product->setData('save_rewrites_history', false);
        $product->setUrlKey('new-url');
        $product->setUrlPath('new-path');
        $this->productRepository->save($product);

        $expected = [
            [
                'request_path' => "new-url.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];

        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @magentoAppIsolation enabled
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAddAndRemoveProductFromWebsite()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore2 = $this->storeManager->getStore('fixture_second_store');
        $testStore3 = $this->storeManager->getStore('fixture_third_store');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
        ];

        //Product in 1st website. Should result in being in 1st and 4th stores.
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }

        //Add product to websites corresponding to all 4 stores.
        //Rewrites should be present for all stores.
        $product->setWebsiteIds(
            array_unique(
                [
                    $testStore1->getWebsiteId(),
                    $testStore2->getWebsiteId(),
                    $testStore3->getWebsiteId(),
                    $testStore4->getWebsiteId(),
                ]
            )
        );
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore3->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ]
        ];

        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }

        //Remove product from stores 1 and 4 and leave assigned to stores 2 and 3.
        $product->setWebsiteIds(
            array_unique(
                [
                    $testStore2->getWebsiteId(),
                    $testStore3->getWebsiteId(),
                ]
            )
        );
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore3->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testChangeVisibilityGlobalScope()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore2 = $this->storeManager->getStore('fixture_second_store');
        $testStore3 = $this->storeManager->getStore('fixture_third_store');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
            UrlRewrite::ENTITY_ID => $product->getId(),
        ];

        //Product in 1st website. Should result in being in 1st and 4th stores.
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ]
        ];
        $actual = $this->getActualResults($productFilter);
        $this->assertEqualsCanonicalizing($expected, array_values($actual));

        /** @var Product $product*/
        $store4Product = $this->objectManager->get(ProductFactory::class)->create();
        $store4Product->setStoreId($testStore4->getId());
        $store4Product->setSku($product->getSku());
        $store4Product->setUrlKey('product-1-store4');
        $this->productRepository->save($store4Product);

        //Set product to be not visible at global scope
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $this->productRepository->save($product);
        $this->assertEmpty($this->getActualResults($productFilter));

        //Add product to websites corresponding to all 4 stores.
        //Rewrites should not be present as the product is hidden
        //at the global scope.
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
        $product->setWebsiteIds(
            array_unique(
                [
                    $testStore1->getWebsiteId(),
                    $testStore2->getWebsiteId(),
                    $testStore3->getWebsiteId(),
                    $testStore4->getWebsiteId(),
                ]
            )
        );
        $this->productRepository->save($product);
        $actual = $this->getActualResults($productFilter);
        $this->assertEmpty($actual);

        //Set product to be visible at global scope
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore3->getId(),
            ],
            [
                'request_path' => "product-1-store4.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        $this->assertEqualsCanonicalizing($expected, array_values($actual));
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_website_with_two_stores.php
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/product_rewrite_multistore.php
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testChangeVisibilityLocalScope()
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore2 = $this->storeManager->getStore('fixture_second_store');
        $testStore3 = $this->storeManager->getStore('fixture_third_store');
        $testStore4 = $this->storeManager->getStore('test');

        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);

        /** @var Product $product*/
        $product = $this->productRepository->get('product1');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
        ];

        //Product in 1st website. Should result in being in 1st and 4th stores.
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }

        //Set product to be not visible at store 4 scope
        //Rewrite should only be present for store 1
        $this->storeManager->setCurrentStore($testStore4);
        $product = $this->productRepository->get('product1', true, $testStore4->getId());
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId()
            ]
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }
        self::assertCount(count($expected), $actual);

        //Add product to websites corresponding to all 4 stores.
        //Rewrites should be present for stores 1,2 and 3.
        //No rewrites should be present for store 4 as that is not visible
        //at local scope.
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
        $product = $this->productRepository->get('product1');
        $product->getExtensionAttributes()->setWebsiteIds(
            array_unique(
                [
                    $testStore1->getWebsiteId(),
                    $testStore2->getWebsiteId(),
                    $testStore3->getWebsiteId(),
                    $testStore4->getWebsiteId()
                ],
            )
        );
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore3->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContains($row, $actual);
        }

        //Set product to be visible at store 4 scope only
        $this->storeManager->setCurrentStore($testStore4);
        $product = $this->productRepository->get('product1');
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $this->productRepository->save($product);
        $expected = [
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore3->getId(),
            ],
            [
                'request_path' => "product-1.html",
                'target_path' => "catalog/product/view/id/" . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore4->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($expected as $row) {
            $this->assertContainsEquals($row, $actual);
        }
    }

    #[
        DataFixture(WebsiteFixture::class, as: 'website'),
        DataFixture(GroupFixture::class, ['website_id' => '$website.id$'], 'store_group'),
        DataFixture(StoreFixture::class, ['store_group_id' => '$store_group.id$'], 'store'),
        DataFixture(ProductFixture::class, ['sku' => 'simple1', 'website_ids' => [1,'$website.id$'], 'product']),
    ]
    public function testRemoveProductFromAllWebsites(): void
    {
        $testStore1 = $this->storeManager->getStore('default');
        $testStore2 = $this->fixtures->get('store');

        $productFilter = [UrlRewrite::ENTITY_TYPE => 'product'];

        /** @var Product $product*/
        $product = $this->productRepository->get('simple1');
        $product->setWebsiteIds([])
            ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $this->productRepository->save($product);
        $unexpected = [
            [
                'request_path' => 'simple1.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore1->getId(),
            ],
            [
                'request_path' => 'simple1.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $testStore2->getId(),
            ],
        ];
        $actual = $this->getActualResults($productFilter);
        foreach ($unexpected as $row) {
            $this->assertNotContains($row, $actual);
        }
    }

    #[
        AppIsolation(true),
        DbIsolation(true),
        DataFixtureBeforeTransaction(
            StoreFixture::class,
            ['store_group_id' => 1, 'website_id' => 1],
            as: 'store2'
        ),
        DataFixture(CategoryFixture::class, as: 'category'),
        DataFixture(ProductFixture::class, ['category_ids' => ['$category.id$']], as: 'product')
    ]
    public function testNotVisibleOnDefaultStoreVisibleOnDefaultScope()
    {
        $category = $this->fixtures->get('category');
        $product = $this->fixtures->get('product');
        $secondStore = $this->fixtures->get('store2');

        $this->urlPersist->deleteByData(
            [
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID => [1, $secondStore->getId()],
            ]
        );

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
            'entity_id' => $product->getId(),
            'store_id' => [$secondStore->getId()]
        ];

        $actualResults = $this->getActualResults($productFilter);
        $this->assertCount(0, $actualResults);

        $product->setStoreId(0);
        $this->productRepository->save($product);

        $actualResults = $this->getActualResults($productFilter);
        $this->assertCount(2, $actualResults);

        $expected = [
            [
                'request_path' => $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $secondStore->getId(),
            ],
            [
                'request_path' => $category->getUrlKey() . '/' . $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId() . '/category/' . $category->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $secondStore->getId(),
            ]
        ];
        foreach ($expected as $row) {
            $this->assertContains($row, $actualResults);
        }
    }

    #[
        DataFixture(StoreFixture::class, ['group_id' => 1, 'website_id' => 1], as: 'store2'),
        DataFixture(CategoryFixture::class, as: 'category'),
        DataFixture(ProductFixture::class, ['category_ids' => ['$category.id$']], as: 'product')
    ]
    public function testUrlRewriteGenerationBasedOnScopeVisibility()
    {
        $secondStore = $this->fixtures->get('store2');
        $category = $this->fixtures->get('category');
        $product = $this->fixtures->get('product');

        $productFilter = [
            UrlRewrite::ENTITY_TYPE => 'product',
            'entity_id' => $product->getId(),
            'store_id' => [1, $secondStore->getId()]
        ];

        $actualResults = $this->getActualResults($productFilter);
        $this->assertCount(4, $actualResults);

        $productScopeStore1 = $this->productRepository->get($product->getSku(), true, 1);
        $productScopeStore1->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $this->productRepository->save($productScopeStore1);

        $actualResults = $this->getActualResults($productFilter);
        $this->assertCount(2, $actualResults);

        $productGlobal = $this->productRepository->get($product->getSku(), true, Store::DEFAULT_STORE_ID);
        $productGlobal->setVisibility(Visibility::VISIBILITY_IN_CATALOG);
        $this->productRepository->save($productGlobal);

        $actualResults = $this->getActualResults($productFilter);
        $this->assertCount(2, $actualResults);

        $expected = [
            [
                'request_path' => $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $secondStore->getId(),
            ],
            [
                'request_path' => $category->getUrlKey() . '/' . $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId() . '/category/' . $category->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $secondStore->getId(),
            ]
        ];

        $unexpected = [
            [
                'request_path' => $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => 1 //not expected url rewrite for store 1
            ],
            [
                'request_path' => $category->getUrlKey() . '/' . $product->getUrlKey() . '.html',
                'target_path' => 'catalog/product/view/id/' . $product->getId() . '/category/' . $category->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => 1,
            ],
            [
                'request_path' => '/'.$product->getUrlKey() . '.html',// not expected anchor root category url rewrite
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => $secondStore->getId(),
            ],
            [
                'request_path' => '/'.$product->getUrlKey() . '.html',// not expected anchor root category url rewrite
                'target_path' => 'catalog/product/view/id/' . $product->getId(),
                'is_auto_generated' => 1,
                'redirect_type' => 0,
                'store_id' => 1,
            ]
        ];

        foreach ($expected as $row) {
            $this->assertContains($row, $actualResults);
        }

        foreach ($unexpected as $row) {
            $this->assertNotContains($row, $actualResults);
        }
    }
}
