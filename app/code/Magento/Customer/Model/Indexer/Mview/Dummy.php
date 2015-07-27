<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Model\Indexer\Mview;

use Magento\Indexer\Model\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class Dummy implements ActionInterface, MviewActionInterface
{
    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function executeList(array $ids)
    {
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function executeRow($id)
    {
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute($ids)
    {
    }
}
