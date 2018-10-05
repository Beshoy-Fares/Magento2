<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogUrlRewrite\Observer;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductUrlKeyAutogeneratorObserver
 *
 * @package Magento\CatalogUrlRewrite\Observer
 */
class ProductUrlKeyAutogeneratorObserver implements ObserverInterface
{
    /**
     * @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
     */
    private $productUrlPathGenerator;

    /**
     * @param ProductUrlPathGenerator $productUrlPathGenerator
     */
    public function __construct(ProductUrlPathGenerator $productUrlPathGenerator)
    {
        $this->productUrlPathGenerator = $productUrlPathGenerator;
    }

    /**
     * Generate url_key and set it on the Product
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var Product $product
         */
        $product = $observer->getEvent()->getProduct();
        $urlKey = $this->productUrlPathGenerator->getUrlKey($product);
        if (null !== $urlKey) {
            $product->setUrlKey($urlKey);
        }
    }
}
