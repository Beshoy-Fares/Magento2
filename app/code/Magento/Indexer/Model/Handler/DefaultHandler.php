<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Model\Handler;

use Magento\Indexer\Model\HandlerInterface;
use Magento\Indexer\Model\SourceInterface;

class DefaultHandler implements HandlerInterface
{
    /**
     * @param \Zend_Db_Select $select
     * @param SourceInterface $source
     * @param array $fieldInfo
     * @return void
     */
    public function prepareSql(\Zend_Db_Select $select, SourceInterface $source, $fieldInfo)
    {
        $select->columns(new \Zend_Db_Expr($source->getEntityName() . '.' . $fieldInfo['origin']), $fieldInfo['name']);
    }

    /**
     * @param \Zend_Db_Select $select
     * @param SourceInterface $source
     * @param array $fieldInfo
     * @return void
     */
    public function prepareData(\Zend_Db_Select $select, SourceInterface $source, $fieldInfo)
    {
        new \Exception('Not implemented yet');
    }
}
