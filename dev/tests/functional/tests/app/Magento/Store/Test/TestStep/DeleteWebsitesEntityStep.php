<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Test\TestStep;

use Magento\Backend\Test\Page\Adminhtml\EditWebsite;
use Magento\Backend\Test\Page\Adminhtml\DeleteWebsite;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backup\Test\Page\Adminhtml\BackupIndex;
use Magento\Config\Test\TestStep\SetupConfigurationStep;
use Magento\Store\Test\Fixture\Store;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\TestStep\TestStepFactory;

/**
<<<<<<< HEAD
 * Test Step for DeleteStoreEntity
=======
 * Test Step for DeleteWebsitesEntity.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class DeleteWebsitesEntityStep implements TestStepInterface
{
    /* tags */
    const MVP = 'yes';
    const SEVERITY = 'S2';
    /* end tags */

    /**
<<<<<<< HEAD
     * Page BackupIndex
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var BackupIndex
     */
    private $backupIndex;

    /**
<<<<<<< HEAD
     * Page StoreIndex
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var StoreIndex
     */
    private $storeIndex;

    /**
<<<<<<< HEAD
     * Page EditWebsite
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var EditWebsite
     */
    private $editWebsite;

    /**
<<<<<<< HEAD
     * Page StoreDelete
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var DeleteWebsite
     */
    private $deleteWebsite;

    /**
<<<<<<< HEAD
     * Fixture factory.
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var FixtureFactory
     */
    private $fixtureFactory;

    /**
<<<<<<< HEAD
     * Fixture factory.
     *
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @var FixtureInterface
     */
    private $item;

    /**
     * @var string
     */
    private $createBackup;

    /**
     * @var TestStepFactory
     */
    private $stepFactory;

    /**
<<<<<<< HEAD
     * Prepare pages for test
=======
     * Prepare pages for test.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     *
     * @param BackupIndex $backupIndex
     * @param StoreIndex $storeIndex
     * @param EditWebsite $editWebsite
     * @param DeleteWebsite $deleteWebsite
     * @param FixtureFactory $fixtureFactory
     * @param FixtureInterface $item
     * @param TestStepFactory $testStepFactory
     * @param string $createBackup
     */
    public function __construct(
        BackupIndex $backupIndex,
        StoreIndex $storeIndex,
        EditWebsite $editWebsite,
        DeleteWebsite $deleteWebsite,
        FixtureFactory $fixtureFactory,
        FixtureInterface $item,
        TestStepFactory $testStepFactory,
        $createBackup = 'No'
    ) {
        $this->storeIndex = $storeIndex;
        $this->editWebsite = $editWebsite;
        $this->backupIndex = $backupIndex;
        $this->deleteWebsite = $deleteWebsite;
        $this->item = $item;
        $this->createBackup = $createBackup;
        $this->fixtureFactory = $fixtureFactory;
        $this->stepFactory = $testStepFactory;
    }

    /**
     * Delete specific Store View.
     *
     * @return void
     */
    public function run()
    {
        /** @var SetupConfigurationStep $enableBackupsStep */
        $enableBackupsStep = $this->stepFactory->create(
            SetupConfigurationStep::class,
            ['configData' => 'enable_backups_functionality']
        );
        $enableBackupsStep->run();
        $this->backupIndex->open()->getBackupGrid()->massaction([], 'Delete', true, 'Select All');
        $this->storeIndex->open();
        $websiteNames = $this->item->getWebsiteIds();
        if (is_array($websiteNames) && count($websiteNames) > 0) {
            $websiteName = end($websiteNames);
            $this->storeIndex->getStoreGrid()->searchAndOpenWebsiteByName($websiteName);
            $this->editWebsite->getFormPageActions()->delete();
            $this->deleteWebsite->getDeleteWebsiteForm()->fillForm(['create_backup' => $this->createBackup]);
            $this->deleteWebsite->getFormPageActions()->delete();
        }
    }
}
