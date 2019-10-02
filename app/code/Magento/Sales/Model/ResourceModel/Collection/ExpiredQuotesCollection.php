<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\ResourceModel\Collection;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Sales\Model\ExpireQuotesFilterFieldsProvider;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ExpiredQuotesCollection
 */
class ExpiredQuotesCollection
{
    /**
     * @var int
     */
    private $secondsInDay = 86400;

    /**
     * @var string
     */
    private $quoteLifetime = 'checkout/cart/delete_quote_after';

    /**
     * @var CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var ExpireQuotesFilterFieldsProvider
     */
    private $expireQuotesFilterFieldsProvider;

    /**
     * @param ScopeConfigInterface $config
     * @param CollectionFactory $collectionFactory
     * @param ExpireQuotesFilterFieldsProvider $expireQuotesFilterFieldsProvider
     */
    public function __construct(
        ScopeConfigInterface $config,
        CollectionFactory $collectionFactory,
        ExpireQuotesFilterFieldsProvider $expireQuotesFilterFieldsProvider = null
    ) {
        $this->config = $config;
        $this->quoteCollectionFactory = $collectionFactory;
        $this->expireQuotesFilterFieldsProvider = $expireQuotesFilterFieldsProvider
            ?? ObjectManager::getInstance()->get(ExpireQuotesFilterFieldsProvider::class);
    }

    /**
     * Gets expired quotes
     *
     * Quote is considered expired if the latest update date
     * of the quote is greater than lifetime threshold
     *
     * @param StoreInterface $store
     * @return AbstractCollection
     */
    public function getExpiredQuotes(StoreInterface $store): AbstractCollection
    {
        $lifetime = $this->config->getValue(
            $this->quoteLifetime,
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
        $lifetime *= $this->secondsInDay;

        /** @var $quotes Collection */
        $quotes = $this->quoteCollectionFactory->create();
        $quotes->addFieldToFilter('store_id', $store->getId());
        $quotes->addFieldToFilter('updated_at', ['to' => date("Y-m-d", time() - $lifetime)]);

        foreach ($this->expireQuotesFilterFieldsProvider->getFields() as $field => $condition) {
            $quotes->addFieldToFilter($field, $condition);
        }

        return $quotes;
    }
}
