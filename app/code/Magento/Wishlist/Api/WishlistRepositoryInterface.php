<?php

namespace Magento\Wishlist\Api;

/**
 * Interface WishlistRepositoryInterface
 * @api
 * @package Magento\Wishlist\Api
 */
interface WishlistRepositoryInterface
{
    /**
     * @param int $customerId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Wishlist\Api\data\WishlistInterface
     */
    public function getWishlistForCustomer($customerId);

    /**
     * @param int $customerId
     * @param int $productId
     * @throws \Magento\Framework\Exception\StateException
     * @return int
     */
    public function addWishlistForCustomer($customerId, $productId);

}
