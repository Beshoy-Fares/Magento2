<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Object;

/**
 * Interface for
 * 1. models which require cache refresh when it is created/updated/deleted
 * 2. blocks which render this information to front-end
 */
interface IdentityInterface
{
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities();
}
