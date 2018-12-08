<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Analytics\ReportXml;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
<<<<<<< HEAD
 * Class ConnectionFactory
 *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 * Creates connection instance for export according to existing one
 * This connection does not use buffered statement, also this connection is not persistent
 */
class ConnectionFactory
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
<<<<<<< HEAD
     * ConnectionFactory constructor.
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @param ResourceConnection $resourceConnection
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ObjectManagerInterface $objectManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->objectManager = $objectManager;
    }

    /**
     * Creates one-time connection for export
     *
     * @param string $connectionName
     * @return AdapterInterface
     */
    public function getConnection($connectionName)
    {
        $connection = $this->resourceConnection->getConnection($connectionName);
        $connectionClassName = get_class($connection);
        $configData = $connection->getConfig();
        $configData['use_buffered_query'] = false;
        unset($configData['persistent']);
        return $this->objectManager->create(
            $connectionClassName,
            [
                'config' => $configData
            ]
        );
    }
}
