<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Response\HeaderProvider;

use Magento\Framework\App\Response\HeaderProvider\HeaderProviderInterface;

/**
 * Class to be used for setting headers with static values
 */
abstract class AbstractHeader implements HeaderProviderInterface
{
    /** @var string */
    protected $name = '';

    /** @var string */
    protected $value = '';

    /**
     * Whether the header should be attached to the response
     *
     * @return bool
     */
    public function canApply()
    {
        return true;
    }

    /**
     * Get header name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get header value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
