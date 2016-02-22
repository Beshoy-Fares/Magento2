<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Url;

/**
 * Route parameters preprocessor interface.
 */
interface RouteParamsPreprocessorInterface
{
    /**
     * Processes route params.
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return array|null
     */
    public function execute($routePath, $routeParams);
}
