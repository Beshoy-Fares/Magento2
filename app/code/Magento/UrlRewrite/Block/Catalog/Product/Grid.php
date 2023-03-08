<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\UrlRewrite\Block\Catalog\Product;

use Magento\Catalog\Block\Adminhtml\Product\Grid as CatalogProductGrid;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

/**
 * Products grid for URL rewrites editing
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends CatalogProductGrid
{
    /**
     * Disable massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);

        $this->addColumn('sku', ['header' => __('SKU'), 'width' => 80, 'index' => 'sku']);
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'width' => 50,
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray()
            ]
        );
        return $this;
    }

    /**
     * Get URL for dispatching grid ajax requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/productGrid', ['_current' => true]);
    }

    /**
     * Return row url for js event handlers
     *
     * @param Product|DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', ['product' => $row->getId()]) . 'category';
    }
}
