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
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml order create gift message block
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Giftmessage extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Magento\Adminhtml\Model\Giftmessage\Save
     */
    protected $_giftMessageSave;

    /**
     * @param \Magento\Adminhtml\Model\Giftmessage\Save $giftMessageSave
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Model\Giftmessage\Save $giftMessageSave,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftMessageSave = $giftMessageSave;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    /**
     * Generate form for editing of gift message for entity
     *
     * @param \Magento\Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(\Magento\Object $entity, $entityType='quote') {
        return $this->getLayout()
            ->createBlock('Magento\Sales\Block\Adminhtml\Order\Create\Giftmessage\Form')
            ->setEntity($entity)
            ->setEntityType($entityType)
            ->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
               && $this->helper('Magento\GiftMessage\Helper\Message')->getIsMessagesAvailable('item',
                        $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
            return $items;
        }

        return false;
    }

    /**
     * Retrieve gift message save model
     *
     * @return \Magento\Adminhtml\Model\Giftmessage\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_giftMessageSave;
    }

}
