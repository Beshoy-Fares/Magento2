<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class ProductStoreFilter implements CustomFilterInterface
{
    /**
     * Apply store Filter to Product Collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool Whether the filter is applied
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
        $storeFilter = [$conditionType => [$filter->getValue()]];

        /** @var Collection $collection */
        $collection->addStoreFilter($storeFilter);

        return true;
    }
}
