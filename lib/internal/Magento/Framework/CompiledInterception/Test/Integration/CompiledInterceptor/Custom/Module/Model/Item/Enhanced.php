<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile
namespace Magento\Framework\CompiledInterception\Test\Integration\CompiledInterceptor\Custom\Module\Model\Item;

class Enhanced extends \Magento\Framework\CompiledInterception\Test\Integration\CompiledInterceptor\Custom\Module\Model\Item
{
    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst(parent::getName());
    }
}
