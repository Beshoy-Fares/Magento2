<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Model;

interface FieldsetInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function update(array $data);
}
