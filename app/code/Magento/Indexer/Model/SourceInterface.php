<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Model;

interface SourceInterface
{
    /**
     * @return string
     */
    public function getEntityName();
}
