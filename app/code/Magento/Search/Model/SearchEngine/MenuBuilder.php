<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Search\Model\SearchEngine;

use \Magento\Backend\Model\Menu;
use \Magento\Backend\Model\Menu\Builder;
use \Magento\Framework\Search\SearchEngine\ConfigInterface;
use \Magento\Search\Model\EngineResolver;
use Magento\Backend\Model\Menu\Config as MenuConfig;

/**
 * A plugin for Magento\Backend\Model\Menu\Builder class. Implements "after" for "getResult()".
 *
 * The purpose of this plugin is to go through the menu tree and remove "Search Terms" menu item if the
 * selected search engine does not support "synonyms" feature.
 */
class MenuBuilder
{
    /**
     * A constant to refer to "Search Synonyms" menu item id from etc/adminhtml/menu.xml
     */
    const SEARCH_SYNONYMS_MENU_ITEM_ID = 'Magento_Search::search_synonyms';

    /*
     * @var MenuConfig $menuConfig
     */
    protected $menuConfig;

    /**
     * @var ConfigInterface $searchFeatureConfig
     */
    protected $searchFeatureConfig;

    /**
     * @var EngineResolver $engineResolver
     */
    protected $engineResolver;

    /**
     * MenuBuilder constructor.
     *
     * @param MenuConfig $menuConfig
     * @param ConfigInterface $searchFeatureConfig
     * @param EngineResolver $engineResolver
     */
    public function __construct(
        MenuConfig $menuConfig,
        ConfigInterface $searchFeatureConfig,
        EngineResolver $engineResolver
    ) {
        $this->menuConfig = $menuConfig;
        $this->searchFeatureConfig = $searchFeatureConfig;
        $this->engineResolver = $engineResolver;
    }

    /**
     * Removes 'Search Synonyms' from the menu if 'synonyms' is not supported
     *
     * @param Builder $subject
     * @return Menu
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetResult(Builder $subject)
    {
        $menu = $this->menuConfig->getMenu();
        $searchEngine = $this->engineResolver->getCurrentSearchEngine();
        if (!$this->searchFeatureConfig
            ->isFeatureSupported(ConfigInterface::SEARCH_ENGINE_FEATURE_SYNONYMS, $searchEngine)) {

            // "Search Synonyms" feature is not supported by the current configured search engine.
            // Menu will be updated to remove it from the list
            $menu->remove(self::SEARCH_SYNONYMS_MENU_ITEM_ID);
        }
        return $menu;
    }
}
