<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Option\Repository as OptionRepository;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Catalog product copier.
 *
 * Creates product duplicate.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Copier
{
    private const ENTITY_TYPE = 'product';

    /**
     * @var Option\Repository
     */
    protected $optionRepository;

    /**
     * @var CopyConstructorInterface
     */
    protected $copyConstructor;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ResourceConnection
     */
    private $urlRewriteCollectionFactory;

    /**
     * @param CopyConstructorInterface $copyConstructor
     * @param ProductFactory $productFactory
     * @param ScopeOverriddenValue $scopeOverriddenValue
     * @param OptionRepository|null $optionRepository
     * @param MetadataPool|null $metadataPool
     * @param ScopeConfigInterface|null $scopeConfig
     * @param UrlRewriteCollectionFactory|null $urlRewriteCollectionFactory
     */
    public function __construct(
        CopyConstructorInterface $copyConstructor,
        ProductFactory $productFactory,
        ScopeOverriddenValue $scopeOverriddenValue,
        OptionRepository $optionRepository,
        MetadataPool $metadataPool,
        ?ScopeConfigInterface $scopeConfig = null,
        ?UrlRewriteCollectionFactory $urlRewriteCollectionFactory = null
    ) {
        $this->productFactory = $productFactory;
        $this->copyConstructor = $copyConstructor;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->optionRepository = $optionRepository;
        $this->metadataPool = $metadataPool;
        $this->scopeConfig = $scopeConfig ?: ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory
            ?: ObjectManager::getInstance()->get(UrlRewriteCollectionFactory::class);
    }

    /**
     * Create product duplicate
     *
     * @param Product $product
     * @return Product
     */
    public function copy(Product $product): Product
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();

        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);

        /** @var Product $duplicate */
        $duplicate = $this->productFactory->create();
        $productData = $product->getData();
        $productData = $this->removeStockItem($productData);
        $duplicate->setData($productData);
        $duplicate->setOptions([]);
        $duplicate->setMetaTitle(null);
        $duplicate->setMetaKeyword(null);
        $duplicate->setMetaDescription(null);
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalLinkId($product->getData($metadata->getLinkField()));
        $duplicate->setStatus(Status::STATUS_DISABLED);
        $duplicate->setCreatedAt(null);
        $duplicate->setUpdatedAt(null);
        $duplicate->setId(null);
        $duplicate->setStoreId(Store::DEFAULT_STORE_ID);
        $this->copyConstructor->build($product, $duplicate);
        $this->setDefaultUrl($product, $duplicate);
        $this->setStoresUrl($product, $duplicate);
        $this->optionRepository->duplicate($product, $duplicate);

        return $duplicate;
    }

    /**
     * Set default URL.
     *
     * @param Product $product
     * @param Product $duplicate
     * @return void
     */
    private function setDefaultUrl(Product $product, Product $duplicate) : void
    {
        $duplicate->setStoreId(Store::DEFAULT_STORE_ID);
        $resource = $product->getResource();
        $productId = $product->getId();
        $urlKey = $resource->getAttributeRawValue($productId, 'url_key', Store::DEFAULT_STORE_ID);
        do {
            $urlKey = $this->modifyUrl($urlKey);
            $duplicate->setUrlKey($urlKey);
        } while ($this->isUrlRewriteExists($urlKey));
        $duplicate->setData('url_path', null);
        $duplicate->save();
    }

    /**
     * Set URL for each store.
     *
     * @param Product $product
     * @param Product $duplicate
     *
     * @return void
     * @throws UrlAlreadyExistsException
     */
    private function setStoresUrl(Product $product, Product $duplicate) : void
    {
        $storeIds = $duplicate->getStoreIds();
        $productId = $product->getId();
        $productResource = $product->getResource();
        $attribute = $productResource->getAttribute('url_key');
        $duplicate->setData('save_rewrites_history', false);
        foreach ($storeIds as $storeId) {
            $useDefault = !$this->scopeOverriddenValue->containsValue(
                ProductInterface::class,
                $product,
                'url_key',
                $storeId
            );
            if ($useDefault) {
                continue;
            }

            $duplicate->setStoreId($storeId);
            $urlKey = $productResource->getAttributeRawValue($productId, 'url_key', $storeId);
            $iteration = 0;

            do {
                if ($iteration === 10) {
                    throw new UrlAlreadyExistsException();
                }

                $urlKey = $this->modifyUrl($urlKey);
                $duplicate->setUrlKey($urlKey);
                $iteration++;
            } while (!$attribute->getEntity()->checkAttributeUniqueValue($attribute, $duplicate));
            $duplicate->setData('url_path', null);
            $productResource->saveAttribute($duplicate, 'url_path');
            $productResource->saveAttribute($duplicate, 'url_key');
        }
        $duplicate->setStoreId(Store::DEFAULT_STORE_ID);
    }

    /**
     * Modify URL key.
     *
     * @param string $urlKey
     * @return string
     */
    private function modifyUrl(string $urlKey) : string
    {
        return preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
            ? $matches[1] . '-' . ($matches[2] + 1)
            : $urlKey . '-1';
    }

    /**
     * Remove stock item
     *
     * @param array $productData
     * @return array
     */
    private function removeStockItem(array $productData): array
    {
        if (isset($productData[ProductInterface::EXTENSION_ATTRIBUTES_KEY])) {
            $extensionAttributes = $productData[ProductInterface::EXTENSION_ATTRIBUTES_KEY];
            if (null !== $extensionAttributes->getStockItem()) {
                $extensionAttributes->setData('stock_item', null);
            }
        }
        return $productData;
    }

    /**
     * Verify if generated url rewrite exists in url_rewrite table
     *
     * @param string $urlKey
     * @return bool
     */
    private function isUrlRewriteExists(string $urlKey): bool
    {
        $urlRewriteCollection = $this->urlRewriteCollectionFactory->create();
        $urlRewriteCollection->addFieldToFilter(UrlRewrite::ENTITY_TYPE, self::ENTITY_TYPE)
            ->addFieldToFilter(UrlRewrite::REQUEST_PATH, $urlKey . $this->getUrlSuffix());

        return $urlRewriteCollection->getSize() !== 0;
    }

    /**
     * Returns default product url suffix config
     *
     * @return string|null
     */
    private function getUrlSuffix(): ?string
    {
        return $this->scopeConfig->getValue(ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX);
    }
}
