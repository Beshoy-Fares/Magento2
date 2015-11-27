<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeTreeProviderInterface;

class ScopeTreeProvider implements ScopeTreeProviderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        $defaultScope = [
            'scope' => ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            'scope_id' => null,
            'scopes' => [],
        ];

        /** @var \Magento\Store\Model\Website $website */
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteScope = [
                'scope' => ScopeInterface::SCOPE_WEBSITE,
                'scope_id' => $website->getId(),
                'scopes' => [],
            ];

            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                $groupScope = [
                    'scope' => ScopeInterface::SCOPE_GROUP,
                    'scope_id' => $group->getId(),
                    'scopes' => [],
                ];

                /** @var \Magento\Store\Model\Group $store */
                foreach ($group->getStores() as $store) {
                    $storeScope = [
                        'scope' => ScopeInterface::SCOPE_STORE,
                        'scope_id' => $store->getId(),
                        'scopes' => [],
                    ];
                    $groupScope['scopes'][] = $storeScope;
                }
                $websiteScope['scopes'][] = $groupScope;
            }
            $defaultScope['scopes'][] = $websiteScope;
        }
        return $defaultScope;
    }
}
