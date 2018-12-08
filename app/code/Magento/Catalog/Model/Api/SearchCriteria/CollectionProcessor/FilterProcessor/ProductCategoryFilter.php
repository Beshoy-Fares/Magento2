<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class ProductCategoryFilter implements CustomFilterInterface
{
    /**
     * Apply category_id Filter to Product Collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool Whether the filter is applied
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $value = $filter->getValue();
        $conditionType = $filter->getConditionType() ?: 'in';
<<<<<<< HEAD
        if (($conditionType === 'in' || $conditionType === 'nin') && is_string($value)) {
            $value = explode(',', $value);
        } else {
            $value = [$value];
        }
        $categoryFilter = [$conditionType => $value];
=======
        $filterValue = [$value];
        if (($conditionType === 'in' || $conditionType === 'nin') && is_string($value)) {
            $filterValue = explode(',', $value);
        }
        $categoryFilter = [$conditionType => $filterValue];
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3

        /** @var Collection $collection */
        $collection->addCategoriesFilter($categoryFilter);

        return true;
    }
}
