<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Controller;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class PathProcessor
{
    /**  Store code alias to indicate that all stores should be affected by action */
    public const ALL_STORE_CODE = 'all';

    /**
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private ?ResolverInterface $localeResolver = null
    ) {
        $this->localeResolver = $localeResolver ?: ObjectManager::getInstance()->get(
            ResolverInterface::class
        );
    }

    /**
     * Process path
     *
     * @param string $pathInfo
     * @return array
     */
    private function stripPathBeforeStorecode($pathInfo)
    {
        $pathParts = explode('/', $pathInfo !== null ? trim($pathInfo, '/') : '');
        array_shift($pathParts);
        $path = '/' . implode('/', $pathParts);
        return explode('/', ltrim($path, '/'), 2);
    }

    /**
     * Process path info
     *
     * @param string $pathInfo
     * @return string
     * @throws NoSuchEntityException
     */
    public function process($pathInfo)
    {
        $pathParts = $this->stripPathBeforeStorecode($pathInfo);
        $storeCode = current($pathParts);
        $stores = $this->storeManager->getStores(false, true);
        if (isset($stores[$storeCode])) {
            $this->storeManager->setCurrentStore($storeCode);
            $this->localeResolver->emulate($this->storeManager->getStore()->getId());
            $path = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
        } elseif ($storeCode === self::ALL_STORE_CODE) {
            $this->storeManager->setCurrentStore(\Magento\Store\Model\Store::ADMIN_CODE);
            $this->localeResolver->emulate($this->storeManager->getStore()->getId());
            $path = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
        } else {
            $path = '/' . implode('/', $pathParts);
        }
        return $path;
    }
}
