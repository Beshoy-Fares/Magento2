<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Config;

use Magento\Framework\App\ScopeResolverPool;

/**
 * Class for resolving scope code
 */
class ScopeCodeResolver
{
    /**
     * @var ScopeResolverPool
     */
    private $scopeResolverPool;

    /**
     * @var array
     */
    private $resolvedScopeCodes = [];

    /**
     * @param ScopeResolverPool $scopeResolverPool
     */
    public function __construct(ScopeResolverPool $scopeResolverPool)
    {
        $this->scopeResolverPool = $scopeResolverPool;
    }

    /**
     * Resolve scope code
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function resolve($scopeType, $scopeCode = null)
    {
        if (isset($this->resolvedScopeCodes[$scopeType][$scopeCode])) {
            return $this->resolvedScopeCodes[$scopeType][$scopeCode];
        }
        if (($scopeCode === null || is_numeric($scopeCode))
            && $scopeType !== ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) {
            $scopeResolver = $this->scopeResolverPool->get($scopeType);
            $resolverScopeCode = $scopeResolver->getScope($scopeCode);
            if ($scopeCode === null) {
                $scopeCode = $resolverScopeCode->getCode();
            }
        } else {
            $resolverScopeCode = $scopeCode;
        }

        if ($resolverScopeCode instanceof \Magento\Framework\App\ScopeInterface) {
            $resolverScopeCode = $resolverScopeCode->getCode();
        }

        $this->resolvedScopeCodes[$scopeType][$scopeCode] = $resolverScopeCode;
        return $resolverScopeCode;
    }

    /**
     * Clean resolvedScopeCodes, store codes may have been renamed
     *
     * @return void
     */
    public function clean()
    {
        $this->resolvedScopeCodes = [];
    }
}
