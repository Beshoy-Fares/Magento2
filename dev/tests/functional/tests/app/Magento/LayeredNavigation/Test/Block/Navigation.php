<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\LayeredNavigation\Test\Block;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Catalog layered navigation view block.
 */
class Navigation extends Block
{
    /**
     * 'Clear All' link.
     *
     * @var string
     */
    protected $clearAll = '.action.clear';

    /**
     * Attribute option title selector.
     *
     * @var string
     */
    protected $optionTitle = './/div[@class="filter-options-title" and contains(text(),"%s")]';

    /**
     * Filter link locator.
     *
     * @var string
     */
    protected $filterLink = './/div[@class="filter-options-title" and contains(text(),"%s")]/following-sibling::div//a';

    /**
     * Locator value for "Expand Filter" button
     *
     * @var string
     */
    protected $expandFilterButton = '[data]';

    /**
     * Click on 'Clear All' link.
     *
     * @return void
     */
    public function clearAll()
    {
        $this->_rootElement->find($this->clearAll, locator::SELECTOR_CSS)->click();
    }

    /**
     * Get array of available filters.
     *
     * @return array
     */
    public function getFilters()
    {
        $options = $this->_rootElement->getElements(sprintf($this->optionTitle, ''), Locator::SELECTOR_XPATH);
        $data = [];
        foreach ($options as $option) {
            $data[] = $option->getText();
        }
        return $data;
    }

    /**
     * Apply Layerd Navigation filter.
     *
     * @param string $filter
     * @param string $linkPattern
     * @return void
     * @throws \Exception
     */
    public function applyFilter($filter, $linkPattern)
    {
        $expandFilterButton = sprintf($this->optionTitle, $filter);
        $links = sprintf($this->filterLink, $filter);

        if (!$this->_rootElement->find($links, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->_rootElement->find($expandFilterButton, Locator::SELECTOR_XPATH)->click();
        }

        $links = $this->_rootElement->getElements($links, Locator::SELECTOR_XPATH);
        foreach ($links as $link) {
            if (preg_match($linkPattern, trim($link->getText()))) {
                $link->click();
                return;
            }
        }
        throw new \Exception("Can't find {$filter} filter link by pattern: {$linkPattern}");
    }
}
