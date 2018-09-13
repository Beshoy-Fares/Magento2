<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/**
 * \Magento\Framework\Lock\Backend\Database test case
 */
namespace Magento\Framework\Lock\Backend;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\Lock\Backend\Database
     */
    private $model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(\Magento\Framework\Lock\Backend\Database::class);
    }

    /**
     * @return void
     */
    public function testLockAndUnlock(): void
    {
        $name = 'test_lock';

        $this->assertFalse($this->model->isLocked($name));

        $this->assertTrue($this->model->lock($name));
        $this->assertTrue($this->model->isLocked($name));

        $this->assertTrue($this->model->unlock($name));
        $this->assertFalse($this->model->isLocked($name));
    }

    /**
     * @return void
     */
    public function testUnlockWithoutExistingLock(): void
    {
        $name = 'test_lock';

        $this->assertFalse($this->model->isLocked($name));
        $this->assertFalse($this->model->unlock($name));
    }
}
