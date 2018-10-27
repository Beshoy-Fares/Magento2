<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> upstream/2.2-develop
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Category;

use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;

/**
<<<<<<< HEAD
 * Generate and save url-rewrites for category if its parent have specified url-key for different store views.
=======
 * Generate and save url-rewrites for category if its parent have specified url-key for different store views
>>>>>>> upstream/2.2-develop
 */
class UpdateUrlPath
{
    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    private $categoryUrlPathGenerator;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator
     */
    private $categoryUrlRewriteGenerator;

    /**
     * @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService
     */
    private $storeViewService;

    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param StoreViewService $storeViewService
     */
    public function __construct(
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        StoreViewService $storeViewService
    ) {
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->categoryUrlRewriteGenerator = $categoryUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->storeViewService = $storeViewService;
    }

    /**
<<<<<<< HEAD
     * Perform url updating for different stores.
=======
     * Perform url updating for different stores
>>>>>>> upstream/2.2-develop
     *
     * @param CategoryResource $subject
     * @param CategoryResource $result
     * @param AbstractModel $category
     * @return CategoryResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        CategoryResource $subject,
        CategoryResource $result,
        AbstractModel $category
<<<<<<< HEAD
    ): CategoryResource {
        $parentCategoryId = $category->getParentId();
        if ($category->isObjectNew()
            && !$category->isInRootCategoryList()
            && !empty($parentCategoryId)
        ) {
            foreach ($category->getStoreIds() as $storeId) {
                if (!$this->isGlobalScope((int)$storeId)
=======
    ) {
        $parentCategoryId = $category->getParentId();
        if ($category->isObjectNew()
            && !$category->isInRootCategoryList()
            && !empty($parentCategoryId)) {
            foreach ($category->getStoreIds() as $storeId) {
                if (!$this->isGlobalScope($storeId)
>>>>>>> upstream/2.2-develop
                    && $this->storeViewService->doesEntityHaveOverriddenUrlPathForStore(
                        $storeId,
                        $parentCategoryId,
                        Category::ENTITY
                    )
                ) {
                    $category->setStoreId($storeId);
                    $this->updateUrlPathForCategory($category, $subject);
                    $this->urlPersist->replace($this->categoryUrlRewriteGenerator->generate($category));
                }
            }
        }
<<<<<<< HEAD

=======
>>>>>>> upstream/2.2-develop
        return $result;
    }

    /**
<<<<<<< HEAD
     * Check that store id is in global scope.
     *
     * @param int $storeId
=======
     * Check that store id is in global scope
     *
     * @param int|null $storeId
>>>>>>> upstream/2.2-develop
     * @return bool
     */
    private function isGlobalScope(int $storeId): bool
    {
<<<<<<< HEAD
        return $storeId === Store::DEFAULT_STORE_ID;
    }

    /**
     * Updates category url path.
     *
     * @param Category $category
     * @param CategoryResource $categoryResource
     * @return void
     */
    private function updateUrlPathForCategory(Category $category, CategoryResource $categoryResource): void
=======
        return null === $storeId || $storeId === Store::DEFAULT_STORE_ID;
    }

    /**
     * @param Category $category
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     */
    private function updateUrlPathForCategory(Category $category, CategoryResource $categoryResource)
>>>>>>> upstream/2.2-develop
    {
        $category->unsUrlPath();
        $category->setUrlPath($this->categoryUrlPathGenerator->getUrlPath($category));
        $categoryResource->saveAttribute($category, 'url_path');
    }
}
