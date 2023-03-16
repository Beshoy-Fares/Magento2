<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRule\Model\Validator;

/**
 * Class Pool collects custom validators for items before SalesRules are applied
 */
class Pool
{
    /**
     * @param array $validators
     */
    public function __construct(
        protected readonly array $validators = []
    ) {
    }

    /**
     * Get Validators defined in di
     *
     * @param string $type
     * @return array
     */
    public function getValidators($type)
    {
        return isset($this->validators[$type]) ? $this->validators[$type] : [];
    }
}
