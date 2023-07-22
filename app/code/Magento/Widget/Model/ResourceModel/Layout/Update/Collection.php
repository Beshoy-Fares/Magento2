<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Widget\Model\ResourceModel\Layout\Update;

use DateInterval;
use DateTimeZone;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime;
use Magento\Widget\Model\Layout\Update;
use Magento\Widget\Model\ResourceModel\Layout\Update as ResourceUpdate;
use Psr\Log\LoggerInterface;

/**
 * Layout update collection model
 */
class Collection extends AbstractCollection
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'layout_update_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'layout_update_collection';

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param DateTime $dateTime
     * @param mixed $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        protected readonly DateTime $dateTime,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            Update::class,
            ResourceUpdate::class
        );
    }

    /**
     * Add filter by theme id
     *
     * @param int $themeId
     * @return $this
     */
    public function addThemeFilter($themeId)
    {
        $this->_joinWithLink();
        $this->getSelect()->where('link.theme_id = ?', $themeId);

        return $this;
    }

    /**
     * Add filter by store id
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->_joinWithLink();
        $this->getSelect()->where('link.store_id = ?', $storeId);

        return $this;
    }

    /**
     * Join with layout link table
     *
     * @return $this
     */
    protected function _joinWithLink()
    {
        $flagName = 'joined_with_link_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()->join(
                ['link' => $this->getTable('layout_link')],
                'link.layout_update_id = main_table.layout_update_id',
                ['store_id', 'theme_id']
            );

            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Left Join with layout link table
     *
     * @param array $fields
     * @return $this
     */
    protected function _joinLeftWithLink($fields = [])
    {
        $flagName = 'joined_left_with_link_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()->joinLeft(
                ['link' => $this->getTable('layout_link')],
                'link.layout_update_id = main_table.layout_update_id',
                [$fields]
            );
            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Get layouts that are older then specified number of days
     *
     * @param string $days
     * @return $this
     */
    public function addUpdatedDaysBeforeFilter($days)
    {
        $datetime = new \DateTime('now', new DateTimeZone('UTC'));
        $storeInterval = new DateInterval('P' . $days . 'D');
        $datetime->sub($storeInterval);
        $formattedDate = $this->dateTime->formatDate($datetime->getTimestamp());

        $this->addFieldToFilter(
            'main_table.updated_at',
            ['notnull' => true]
        )->addFieldToFilter(
            'main_table.updated_at',
            ['lt' => $formattedDate]
        );

        return $this;
    }

    /**
     * Get layouts without links
     *
     * @return $this
     */
    public function addNoLinksFilter()
    {
        $this->_joinLeftWithLink();
        $this->addFieldToFilter('link.layout_update_id', ['null' => true]);

        return $this;
    }

    /**
     * Delete updates in collection
     *
     * @return $this
     */
    public function delete()
    {
        /** @var $update Update */
        foreach ($this->getItems() as $update) {
            $update->delete();
        }
        return $this;
    }
}
