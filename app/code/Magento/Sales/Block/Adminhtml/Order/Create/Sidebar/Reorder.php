<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Sidebar;

/**
 * Adminhtml sales order create sidebar cart block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Reorder extends \Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\AbstractSidebar
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_order_item';

    /**
     * Orders factory
     *
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $_ordersFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $ordersFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Model\Resource\Order\CollectionFactory $ordersFactory,
        array $data = array()
    ) {
        $this->_ordersFactory = $ordersFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $salesConfig, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_reorder');
        $this->setDataId('reorder');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Last Ordered Items');
    }

    /**
     * Retrieve last order on current website
     *
     * @return \Magento\Sales\Model\Order|false
     */
    public function getLastOrder()
    {
        $storeIds = $this->getQuote()->getStore()->getWebsite()->getStoreIds();
        $collection = $this->_ordersFactory->create()->addFieldToFilter(
            'customer_id',
            $this->getCustomerId()
        )->addFieldToFilter(
            'store_id',
            array('in' => $storeIds)
        )->setOrder(
            'created_at',
            'desc'
        )->setPageSize(
            1
        )->load();
        foreach ($collection as $order) {
            return $order;
        }

        return false;
    }

    /**
     * Retrieve item collection
     *
     * @return array|false
     */
    public function getItemCollection()
    {
        if ($order = $this->getLastOrder()) {
            $items = array();
            foreach ($order->getItemsCollection() as $item) {
                if (!$item->getParentItem()) {
                    $items[] = $item;
                }
            }
            return $items;
        }
        return false;
    }

    /**
     * Retrieve display item qty availability
     *
     * @return false
     */
    public function canDisplayItemQty()
    {
        return false;
    }

    /**
     * Retrieve remove items availability
     *
     * @return false
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * Retrieve display price availability
     *
     * @return false
     */
    public function canDisplayPrice()
    {
        return false;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param \Magento\Framework\Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }
}
