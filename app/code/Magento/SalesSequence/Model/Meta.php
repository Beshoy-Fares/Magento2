<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesSequence\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\SalesSequence\Model\ResourceModel\Meta as ResourceMeta;

/**
 * Class Meta
 *
 * @api
 * @since 100.0.2
 */
class Meta extends AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceMeta::class);
    }
}
