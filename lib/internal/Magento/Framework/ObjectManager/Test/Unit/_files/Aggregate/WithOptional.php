<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Test\Di\Aggregate;

class WithOptional
{
    public $parent;

    public $child;

    /**
     * WithOptional constructor.
<<<<<<< HEAD
=======
     *
>>>>>>> upstream/2.2-develop
     * @param \Magento\Test\Di\DiParent|null $parent
     * @param \Magento\Test\Di\Child|null $child
     */
    public function __construct(\Magento\Test\Di\DiParent $parent = null, \Magento\Test\Di\Child $child = null)
    {
        $this->parent = $parent;
        $this->child = $child;
    }
}
