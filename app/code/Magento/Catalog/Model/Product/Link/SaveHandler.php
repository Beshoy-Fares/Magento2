<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Link;

use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Link;
use Magento\Framework\Model\Entity\MetadataPool;

/**
 * Class SaveProductLinks
 */
class SaveHandler
{
    /**
     * @var ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Link
     */
    private $linkResource;

    /**
     * @param MetadataPool $metadataPool
     * @param Link $linkResource
     * @param ProductLinkRepositoryInterface $productLinkRepository
     */
    public function __construct(
        MetadataPool $metadataPool,
        Link $linkResource,
        ProductLinkRepositoryInterface $productLinkRepository
    ) {

        $this->metadataPool = $metadataPool;
        $this->linkResource = $linkResource;
        $this->productLinkRepository = $productLinkRepository;
    }

    /**
     * @param string $entityType
     * @param object $entity
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entityType, $entity)
    {
        $links = $entity->getProductLinks();
        if ($links) {
            $this->deleteUnExistingLinks($links, $entity);
            foreach ($links as $link) {
                $this->productLinkRepository->save($link);
            }
        }
        return $entity;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $links
     * @param \Magento\Catalog\Api\Data\ProductInterface $entity
     * @return void
     */
    protected function deleteUnExistingLinks($links, \Magento\Catalog\Api\Data\ProductInterface $entity)
    {
        foreach ($this->productLinkRepository->getList($entity) as $oldLink) {
            $toDelete = true;
            foreach ($links as $option) {
                if ($oldLink->getLinkedProductSku() === $option->getLinkedProductSku()) {
                    $toDelete = false;
                }
            }
            if ($toDelete) {
                $this->productLinkRepository->delete($oldLink);
            }
        }

    }
}
