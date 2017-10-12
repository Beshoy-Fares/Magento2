<?php
/**
 * Created by PhpStorm.
 * User: patrikpihlstrom
 * Date: 11/10/17
 * Time: 22:12
 */

namespace Magento\CatalogUrlRewrite\Plugin\Eav\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;

class AttributeSetRepository
{
    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist
     */
    private $urlPersist;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     */
    private $productCollection;

    /**
     * AttributeSetRepository constructor.
     * @param \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     */
    public function __construct(UrlPersistInterface $urlPersist, Collection $productCollection)
    {
        $this->urlPersist = $urlPersist;
        $this->productCollection = $productCollection;
    }

    /**
     * Remove product url rewrites when an attribute set is deleted.
     *
     * @param \Magento\Eav\Model\AttributeSetRepository $subject
     * @param callable $proceed
     * @param AttributeSetInterface $attributeSet
     */
    public function aroundDelete(
        \Magento\Eav\Model\AttributeSetRepository $subject,
        callable $proceed,
        AttributeSetInterface $attributeSet
    ) {
        // Get the product ids
        $ids = $this->productCollection->addFieldToFilter('attribute_set_id', $attributeSet->getAttributeSetId())
                                       ->getAllIds();

        // Delete the attribute set
        $result = $proceed($attributeSet);

        // Delete the old product url rewrites
        try {
            $this->urlPersist->deleteByData(['entity_id' => $ids, 'entity_type' => 'product']);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the url rewrite(s): %1', $exception->getMessage()));
        }

        return $result;
    }
}
