<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Observer\Compare;

use Magento\Catalog\Model\Product\Compare\Item;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Catalog Compare Item Model
 */
class BindCustomerLoginObserver implements ObserverInterface
{
    /**
     * @var Item
     */
    private $item;

    /**
     * @param Item $item
     */
    public function __construct(
        Item $item
    ) {
        $this->item = $item;
    }

    /**
     * Customer login bind process
     * @param Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute(Observer $observer)
    {
        $this->item->bindCustomerLogin();

        return $this;
    }
}
