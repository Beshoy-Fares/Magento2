<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

return [
    'di' => [
        'allowed_controllers' => [
            'Magento\Setup\Controller\Index',
            'Magento\Setup\Controller\LandingInstaller',
            'Magento\Setup\Controller\LandingUpdater',
            'Magento\Setup\Controller\CreateBackup',
            'Magento\Setup\Controller\CompleteBackup',
            'Magento\Setup\Controller\Navigation',
            'Magento\Setup\Controller\License',
            'Magento\Setup\Controller\ReadinessCheckInstaller',
            'Magento\Setup\Controller\ReadinessCheckUpdater',
            'Magento\Setup\Controller\Environment',
            'Magento\Setup\Controller\DatabaseCheck',
            'Magento\Setup\Controller\AddDatabase',
            'Magento\Setup\Controller\WebConfiguration',
            'Magento\Setup\Controller\CustomizeYourStore',
            'Magento\Setup\Controller\CreateAdminAccount',
            'Magento\Setup\Controller\Install',
            'Magento\Setup\Controller\Success',
            'Magento\Setup\Controller\Modules',
            'Magento\Setup\Controller\ComponentUpgrade',
            'Magento\Setup\Controller\ComponentUpgradeSuccess',
            'Magento\Setup\Controller\BackupActionItems',
            'Magento\Setup\Controller\Maintenance',
        ],
        'instance' => [
            'preference' => [
                'Zend\EventManager\EventManagerInterface' => 'EventManager',
                'Zend\ServiceManager\ServiceLocatorInterface' => 'ServiceManager',
                'Magento\Framework\DB\LoggerInterface' => 'Magento\Framework\DB\Logger\Null',
                'Magento\Framework\Locale\ConfigInterface' => 'Magento\Framework\Locale\Config',
                'Magento\Framework\Module\ModuleRegistryInterface' => 'Magento\Framework\Module\Registrar',
                'Magento\Framework\Filesystem\DriverInterface' => 'Magento\Framework\Filesystem\Driver\File',
            ],
        ],
    ],
];
