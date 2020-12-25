<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductAlertGraphQl\Model\Resolver\Price;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\ProductAlert\Helper\Data as AlertsHelper;
use Magento\ProductAlert\Model\PriceFactory;

/**
 * Unsubscribe to product price alert
 */
class Unsubscribe implements ResolverInterface
{
    /**
     * @var AlertsHelper
     */
    private $helper;

    /**
     * @var PriceFactory
     */
    private $priceFactory;

    /**
     * @param AlertsHelper $helper
     */
    public function __construct(
        AlertsHelper $helper,
        PriceFactory $priceFactory
    ) {
        $this->helper = $helper;
        $this->priceFactory = $priceFactory;
    }

   /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$this->helper->isPriceAlertAllowed()) {
            throw new GraphQlInputException(__('The product price alerts is currently disabled.'));
        }

        $customerId = $context->getUserId();
        $store = $context->getExtensionAttributes()->getStore();

        /* Guest checking */
        if (null === $customerId || 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on product alerts'));
        }

        $productId = ((int) $args['productId']) ?: null;

        $model = $this->priceFactory->create()
                ->setCustomerId($customerId)
                ->setProductId($productId)
                ->setWebsiteId($store->getWebsiteId())
                ->setStoreId($store->getId())
                ->loadByParam();
        
        if (!$model->getId()) {
            throw new GraphQlInputException(__('The current user isn\'t subscribed to price alert.'));
        }

        $model->delete();

        return true;
    }
}
