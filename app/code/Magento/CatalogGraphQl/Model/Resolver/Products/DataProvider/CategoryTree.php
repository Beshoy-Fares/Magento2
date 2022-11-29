<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider;

use Exception;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\NodeKind;
use Iterator;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\CatalogGraphQl\Model\AttributesJoiner;
use Magento\CatalogGraphQl\Model\Category\DepthCalculator;
use Magento\CatalogGraphQl\Model\Category\LevelCalculator;
use Magento\CatalogGraphQl\Model\Resolver\Categories\DataProvider\Category\CollectionProcessorInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Category tree data provider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryTree
{
    /**
     * In depth we need to calculate only children nodes, so the first wrapped node should be ignored
     */
    private const DEPTH_OFFSET = 1;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var AttributesJoiner
     */
    private $attributesJoiner;

    /**
     * @var DepthCalculator
     */
    private $depthCalculator;

    /**
     * @var LevelCalculator
     */
    private $levelCalculator;

    /**
     * @var MetadataPool
     */
    private $metadata;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributesJoiner $attributesJoiner
     * @param DepthCalculator $depthCalculator
     * @param LevelCalculator $levelCalculator
     * @param MetadataPool $metadata
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributesJoiner $attributesJoiner,
        DepthCalculator $depthCalculator,
        LevelCalculator $levelCalculator,
        MetadataPool $metadata,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->attributesJoiner = $attributesJoiner;
        $this->depthCalculator = $depthCalculator;
        $this->levelCalculator = $levelCalculator;
        $this->metadata = $metadata;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Returns categories tree starting from parent $rootCategoryId
     *
     * @param ResolveInfo $resolveInfo
     * @param int $rootCategoryId
     * @param int $storeId
     * @return Iterator
     * @throws LocalizedException
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTree(ResolveInfo $resolveInfo, int $rootCategoryId, int $storeId): Iterator
    {
        $collection = $this->getCollection($resolveInfo, $rootCategoryId);
        return $collection->getIterator();
    }

    /**
     * Return prepared collection
     *
     * @param ResolveInfo $resolveInfo
     * @param int $rootCategoryId
     * @return Collection
     * @throws LocalizedException
     * @throws Exception
     */
    private function getCollection(ResolveInfo $resolveInfo, int $rootCategoryId) : Collection
    {
        $categoryQuery = $resolveInfo->fieldNodes[0];
        $collection = $this->collectionFactory->create();
        $this->joinAttributesRecursively($collection, $categoryQuery, $resolveInfo);
        $depth = $this->depthCalculator->calculate($resolveInfo, $categoryQuery);
        $level = $this->levelCalculator->calculate($rootCategoryId);

        // If root category is being filter, we've to remove first slash
        if ($rootCategoryId == Category::TREE_ROOT_ID) {
            $regExpPathFilter = sprintf('.*%s/[/0-9]*$', $rootCategoryId);
        } else {
            $regExpPathFilter = sprintf('.*/%s/[/0-9]*$', $rootCategoryId);
        }

        //Add `is_anchor` attribute to selected field
        $collection->addAttributeToSelect('is_anchor');

        //Search for desired part of category tree
        $collection->addPathFilter($regExpPathFilter);

        $collection->addFieldToFilter('level', ['gt' => $level]);
        $collection->addFieldToFilter('level', ['lteq' => $level + $depth - self::DEPTH_OFFSET]);
        $collection->addAttributeToFilter('is_active', 1, "left");
        $collection->setOrder('level');
        $collection->setOrder(
            'position',
            $collection::SORT_ORDER_DESC
        );
        $collection->getSelect()->orWhere(
            $collection->getSelect()
                ->getConnection()
                ->quoteIdentifier(
                    'e.' . $this->metadata->getMetadata(CategoryInterface::class)->getIdentifierField()
                ) . ' = ?',
            $rootCategoryId
        );

        return $collection;
    }

    /**
     * Join attributes recursively
     *
     * @param Collection $collection
     * @param FieldNode $fieldNode
     * @param ResolveInfo $resolveInfo
     * @return void
     */
    private function joinAttributesRecursively(
        Collection $collection,
        FieldNode $fieldNode,
        ResolveInfo $resolveInfo
    ): void {
        if (!isset($fieldNode->selectionSet->selections)) {
            return;
        }

        $subSelection = $fieldNode->selectionSet->selections;
        $this->attributesJoiner->join($fieldNode, $collection, $resolveInfo);

        /** @var FieldNode $node */
        foreach ($subSelection as $node) {
            if ($node->kind === NodeKind::INLINE_FRAGMENT || $node->kind === NodeKind::FRAGMENT_SPREAD) {
                continue;
            }
            $this->joinAttributesRecursively($collection, $node, $resolveInfo);
        }
    }

    /**
     * Returns categories tree starting from parent $rootCategoryId with filtration
     *
     * @param ResolveInfo $resolveInfo
     * @param int $rootCategoryId
     * @param SearchCriteria $searchCriteria
     * @param StoreInterface $store
     * @param array $attributeNames
     * @param ContextInterface $context
     * @return Iterator
     * @throws LocalizedException
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFilteredTree(
        ResolveInfo $resolveInfo,
        int $rootCategoryId,
        SearchCriteria $searchCriteria,
        StoreInterface $store,
        array $attributeNames,
        ContextInterface $context
    ): Iterator {
        $collection = $this->getCollection($resolveInfo, $rootCategoryId);
        $this->collectionProcessor->process($collection, $searchCriteria, $attributeNames, $context);
        return $collection->getIterator();
    }
}
