<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\Backup\Factory;
use Magento\Framework\Setup\BackupRollbackFactory;
use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Setup\Model\WebLogger;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class TakeBackup extends AbstractActionController
{
    /**
     * Handler for maintenance mode
     *
     * @var MaintenanceMode
     */
    private $maintenanceMode;

    /**
     * Factory for BackupRollback
     *
     * @var BackupRollbackFactory
     */
    private $backupRollbackFactory;

    /**
     * @var WebLogger
     */
    private $log;

    /**
     * Constructor
     *
     * @param ObjectManagerProvider $objectManagerProvider
     * @param MaintenanceMode $maintenanceMode
     */
    public function __construct(
        ObjectManagerProvider $objectManagerProvider,
        MaintenanceMode $maintenanceMode,
        WebLogger $logger
    ) {
        $this->objectManager = $objectManagerProvider->get();
        $this->maintenanceMode = $maintenanceMode;
        $this->backupRollbackFactory = $this->objectManager->get('Magento\Framework\Setup\BackupRollbackFactory');
        $this->log = $logger;
    }

    /**
     * Takes backup for code, media or DB
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        try {
            $this->maintenanceMode->set(true);
            $time = time();
            $backupHandler = $this->backupRollbackFactory->create($this->log);
            if ($params['code']) {
                $backupHandler->codeBackup($time);
            }
            if ($params['media']) {
                $backupHandler->codeBackup($time, Factory::TYPE_MEDIA);
            }
            if ($params['db']) {
                $backupHandler->dbBackup($time);
            }
            return new JsonModel(['success' => true]);
        } catch (\Exception $e) {
            $this->maintenanceMode->set(false);
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
