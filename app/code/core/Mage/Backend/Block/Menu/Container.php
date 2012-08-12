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
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend menu item block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Menu_Container extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * Set menu model
     * @return Mage_Backend_Model_Menu
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * Get menu filter iterator
     * @return Mage_Backend_Model_Menu_Filter_Iterator
     */
    public function getMenuIterator()
    {
        return Mage::getModel('Mage_Backend_Model_Menu_Filter_Iterator', $this->getMenu()->getIterator());
    }

    /**
     * Get menu model
     *
     * @param Mage_Backend_Model_Menu $menu
     * @return Mage_Backend_Block_Menu_Container
     */
    public function setMenu(Mage_Backend_Model_Menu $menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Render menu item element
     * @param Mage_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    public function renderMenuItem(Mage_Backend_Model_Menu_Item $menuItem)
    {
        /**
         * Save current level
         */
        $currentLevel = $this->getLevel();

        /**
         * Render child blocks
         * @var Mage_Backend_Block_Menu_Item
         */
        $block = $this->getMenuBlock()->getChildBlock($this->getMenuBlock()->getItemRendererBlock());
        $block->setMenuItem($menuItem);
        $block->setLevel($currentLevel);
        $block->setContainerRenderer($this->getMenuBlock());
        $output = $block->toHtml();

        /**
         * Set current level, because it will be changed in child block
         */
        $this->setLevel($currentLevel);
        return $output;
    }
}
