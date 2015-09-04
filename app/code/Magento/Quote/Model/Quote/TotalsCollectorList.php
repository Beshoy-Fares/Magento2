<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total\Collector;
use Magento\Quote\Model\Quote\Address\Total\CollectorFactory;
use Magento\Quote\Model\Quote\Address\Total\CollectorInterface;

class TotalsCollectorList
{

    /**
     * Total models collector
     *
     * @var \Magento\Quote\Model\Quote\Address\Total\Collector
     */
    protected $totalCollector;

    /**
     * @var \Magento\Quote\Model\Quote\Address\Total\CollectorFactory
     */
    protected $totalCollectorFactory;

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_quote_address';

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = 'quote_address';

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Model\Quote\Address\TotalsListFactory
     */
    protected $totalListFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\TotalFactory
     */
    protected $totalFactory;

    protected $allowedCollectors = array(
        'subtotal',
        'grand_total'
    );


    public function __construct(
        Collector $totalCollector,
        CollectorFactory $totalCollectorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote\Address\TotalsListFactory $totalListFactory,
        \Magento\Quote\Model\Quote\Address\TotalFactory $totalFactory
    ) {
        $this->totalCollector = $totalCollector;
        $this->totalCollectorFactory = $totalCollectorFactory;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->totalListFactory = $totalListFactory;
        $this->totalFactory = $totalFactory;
    }

    /**
     * @param $storeId
     * @return Collector
     */
    private function getTotalCollector($storeId)
    {
        if ($this->totalCollector === null) {
            $store = $this->storeManager->getStore($storeId);

            $this->totalCollector = $this->totalCollectorFactory->create(
                ['store' => $store]
            );
        }
        return $this->totalCollector;
    }

    /**
     * @param $storeId
     * @return \Magento\Quote\Model\Quote\Address\Total\AbstractTotal[]
     */
    public function getCollectors($storeId)
    {
        return $this->getTotalCollector($storeId)->getCollectors();
    }
}
