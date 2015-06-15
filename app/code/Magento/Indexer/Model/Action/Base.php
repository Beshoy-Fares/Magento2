<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Model\Action;

use Magento\Framework\App\Resource as AppResource;
use Magento\Framework\App\Resource\SourceProviderInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Stdlib\String;
use Magento\Indexer\Model\ActionInterface;
use Magento\Indexer\Model\FieldsetPool;
use Magento\Indexer\Model\Processor\Handler;
use Magento\Framework\App\Resource\SourcePool;
use Magento\Indexer\Model\HandlerInterface;

class Base implements ActionInterface
{
    /**
     * @var FieldsetPool
     */
    protected $fieldsetPool;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var SourceProviderInterface[]
     */
    protected $sources;

    /**
     * @var SourceProviderInterface
     */
    protected $primarySource;

    /**
     * @var HandlerInterface[]
     */
    protected $handlers;

    /**
     * @var array
     */
    protected $data;

    protected $columnTypesMap = [
        'varchar'    => ['type' => Table::TYPE_TEXT, 'size' => 255],
        'mediumtext' => ['type' => Table::TYPE_TEXT, 'size' => 16777216],
        'text'       => ['type' => Table::TYPE_TEXT, 'size' => 65536],
    ];
    /**
     * @var array
     */
    protected $filterColumns;

    /**
     * @var array
     */
    protected $searchColumns;

    /**
     * @var SourcePool
     */
    protected $sourcePool;

    /**
     * @var Handler
     */
    protected $handlerProcessor;

    /**
     * @var string
     */
    protected $defaultHandler;
    /**
     * @var String
     */

    /**
     * @var String
     */
    protected $string;

    /**
     * @param AppResource $resource
     * @param SourcePool $sourcePool
     * @param Handler $handlerProcessor
     * @param FieldsetPool $fieldsetPool
     * @param String $string
     * @param string $defaultHandler
     * @param array $data
     */
    public function __construct(
        AppResource $resource,
        SourcePool $sourcePool,
        Handler $handlerProcessor,
        FieldsetPool $fieldsetPool,
        String $string,
        $defaultHandler = 'Magento\Indexer\Model\Handler\DefaultHandler',
        $data = []
    )
    {
        $this->connection = $resource->getConnection('write');
        $this->fieldsetPool = $fieldsetPool;
        $this->data = $data;
        $this->sourcePool = $sourcePool;
        $this->handlerProcessor = $handlerProcessor;
        $this->defaultHandler = $defaultHandler;
        $this->string = $string;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->prepareFields();
        $this->prepareSchema();
        $this->connection->query($this->prepareQuery());
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
    }

    protected function prepareQuery()
    {
        $select = $this->createResultSelect();
        return $this->connection->insertFromSelect(
            $select,
            'index_' . $this->getPrimaryResource()->getMainTable()
        );
    }

    /**
     * Return primary source provider
     *
     * @return SourceProviderInterface
     */
    protected function getPrimaryResource()
    {
        return $this->data['fieldsets'][$this->data['primary']]['source'];
    }

    protected function prepareSchema()
    {
        $this->prepareColumns();
        $newTableName = 'index_' . $this->getPrimaryResource()->getMainTable();
        $table = $this->connection->newTable($newTableName)
            ->setComment($this->string->upperCaseWords($newTableName, '_', ' '));

        $table->addColumn(
            $this->getPrimaryResource()->getIdFieldName(),
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true]
        );

        $columns = array_merge($this->filterColumns, $this->searchColumns);
        foreach ($columns as $column) {
            $table->addColumn($column['name'], $column['type'], $column['size']);
        }
        $this->connection->createTable($table);
    }

    protected function prepareIndexes()
    {
        $tableName = 'index_' . $this->getPrimaryResource()->getMainTable();

        foreach ($this->filterColumns as $column) {
            $this->connection->addIndex(
                $tableName,
                $this->connection->getIndexName($tableName, $column['name']),
                $column['name']
            );
        }

        $fullTextIndex = [];
        foreach ($this->searchColumns as $column) {
            $fullTextIndex[] = $column['name'];
        }

        $this->connection->addIndex(
            $tableName,
            $this->connection->getIndexName($tableName, $fullTextIndex, AdapterInterface::INDEX_TYPE_FULLTEXT),
            $fullTextIndex,
            AdapterInterface::INDEX_TYPE_FULLTEXT

        );
    }

    protected function createResultSelect()
    {
        $select = $this->connection->select();
        $select->from($this->getPrimaryResource()->getMainTable(), $this->getPrimaryResource()->getIdFieldName());
        foreach ($this->data['fieldsets'] as $fieldsetName => $fieldset) {
            if (isset($fieldset['reference']['from']) && isset($fieldset['reference']['to'])) {
                $source = $fieldset['source'];
                /** @var SourceProviderInterface $source */
                $currentEntityName = $source->getMainTable();
                $select->joinInner(
                    $currentEntityName,
                    new \Zend_Db_Expr(
                        $this->getPrimaryResource()->getMainTable() . '.' . $fieldset['reference']['from']
                        . '=' . $currentEntityName . '.' . $fieldset['reference']['to']
                    ),
                    null
                );
            }
            foreach ($fieldset['fields'] as $fieldName => $field) {
                $handler = $field['handler'];
                /** @var HandlerInterface $handler */
                $handler->prepareSql($select, $fieldset['source'], $field);
            }
        }

        return $select;
    }


    protected function prepareColumns()
    {
        foreach ($this->data['fieldsets'] as $fieldset) {
            foreach ($fieldset['fields'] as $fieldName => $field) {
                $columnMap = isset($this->columnTypesMap[$field['dataType']])
                    ? $this->columnTypesMap[$field['dataType']]
                    : ['type' => Table::TYPE_TEXT, 'size' => Table::DEFAULT_TEXT_SIZE];
                switch ($field['type']) {
                    case 'filterable':
                        $this->filterColumns[] = [
                            'name' => $fieldName,
                            'type' => $columnMap['type'],
                            'size' => $columnMap['size'],
                        ];
                        break;
                    case 'searchable':
                        $this->searchColumns[] = [
                            'name' => $fieldName,
                            'type' => $columnMap['type'],
                            'size' => $columnMap['size'],
                        ];
                        break;

                    default:
                        $this->filterColumns[] = [
                            'name' => $fieldName,
                            'type' => $columnMap['type'],
                            'size' => $columnMap['size'],
                        ];
                        $this->searchColumns[] = [
                            'name' => $fieldName,
                            'type' => $columnMap['type'],
                            'size' => $columnMap['size'],
                        ];
                        break;
                }
            }
        }
    }

    protected function prepareFields()
    {
        $this->data['handlers']['defaultHandler'] = $this->defaultHandler;
        $this->handlers = $this->handlerProcessor->process($this->data['handlers']);

        foreach ($this->data['fieldsets'] as $fieldsetName => $fieldset) {
            $this->data['fieldsets'][$fieldsetName]['source'] = $this->sourcePool->get($fieldset['source']);
            $defaultHandler = $this->handlers['defaultHandler'];
            if (isset($fieldset['class'])) {
                $fieldsetObject = $this->fieldsetPool->get($fieldset['class']);
                $this->data['fieldsets'][$fieldsetName] = $fieldsetObject->update($fieldset);

                $defaultHandlerClass = $fieldsetObject->getDefaultHandler();
                $defaultHandler = $this->handlerProcessor->process([$defaultHandlerClass])[0];
            }
            foreach ($fieldset['fields'] as $fieldName => $field) {
                $this->data['fieldsets'][$fieldsetName]['fields'][$fieldName]['handler'] =
                    isset($this->handlers[$field['handler']])
                        ? $this->handlers[$field['handler']]
                        : isset($this->data['fieldsets'][$fieldsetName]['handler'])
                            ? $this->data['fieldsets'][$fieldsetName]['handler']
                            : $defaultHandler;
                $this->data['fieldsets'][$fieldsetName]['fields'][$fieldName]['dataType'] =
                    isset($field['dataType']) ? $field['dataType'] : 'varchar';
            }
        }
    }
}
