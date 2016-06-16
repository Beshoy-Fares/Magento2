<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Test\Block\Cart\Sidebar;

use Magento\Checkout\Test\Block\Cart\Sidebar\Item as ProductItem;

/**
 * Grouped Product item block on mini Cart.
 */
class Item extends ProductItem
{
    /**
     * Quantity input selector.
     *
     * @var string
     */
    protected $qty = '//*[div/div/input[@data-cart-item-id=\'%s\']]/*/following-sibling::*//*[contains(@class,"item-qty")]';

    /**
     * Mini cart price selector.
     *
     * @var string
     */
    protected $price = '//*[div/div/input[@data-cart-item-id=\'%s\']]/*/following-sibling::*//*[contains(@class,"minicart-price")]';

    /**
     * Remove grouped product item from mini cart.
     *
     * @return void
     */
    public function removeItemFromMiniCart()
    {
        foreach ($this->config['associated_cart_items'] as $productItem) {
            /** @var ProductItem $productItem */
            $productItem->removeItemFromMiniCart();
        }
    }

    /**
     * Get product price from mini cart.
     *
     * @return array
     */
    public function getPrice()
    {
        $result = [];
        foreach ($this->config['associated_cart_items'] as $productName => $cartItem) {
            /** @var ProductItem $productItem */
            $result[$productName] = parent::getProductPrice($productName);
        }

        return $result;
    }

    /**
     * Get product qty from mini cart.
     *
     * @return array
     */
    public function getQty()
    {
        $result = [];
        foreach ($this->config['associated_cart_items'] as $productName => $cartItem) {
            /** @var ProductItem $productItem */
            $result[$productName] = parent::getProductQty($productName);
        }

        return $result;
    }
}
