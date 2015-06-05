<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Checkout\Block\Cart\Item\Renderer\Actions;

use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote\Item;

class Generic extends Template
{
    /**
     * @var Item
     */
    protected $item;

    /**
     * Returns current quote item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set current quote item
     *
     * @param Item $item
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Check if product is visible in site visibility
     *
     * @return bool
     */
    public function isProductVisibleInSiteVisibility()
    {
        return $this->getItem()->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Check if cart item is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return (bool)$this->getItem()->getIsVirtual();
    }
}
