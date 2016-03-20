<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\EntityManager\Operation\Read;

use Magento\Framework\EntityManager\Operation\ExtensionPool;

/**
 * Class ReadExtensions
 */
class ReadExtensions
{
    /**
     * @var ExtensionPool
     */
    private $extensionPool;

    /**
     * ReadExtensions constructor.
     *
     * @param ExtensionPool $extensionPool
     */
    public function __construct(
        ExtensionPool $extensionPool
    ) {
        $this->extensionPool = $extensionPool;
    }

    /**
     * @param string $entityType
     * @param object $entity
     * @return object
     */
    public function execute($entityType, $entity)
    {
        $actions = $this->extensionPool->getActions($entityType, 'read');
        foreach ($actions as $action) {
            $entity = $action->execute($entityType, $entity);
        }
        return $entity;
    }
}
