<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Test\Unit\Model\Cron;

use Magento\Setup\Model\Cron\JobDbRollback;

class JobDbRollbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobDbRollback
     */
    private $jobDbRollback;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Setup\BackupRollbackFactory
     */
    private $backupRollbackFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Setup\BackupRollback
     */
    private $backupRollback;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Model\Cron\Status
     */
    private $status;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Cache
     */
    private $cache;

    /*
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\State\CleanupFiles
     */
    private $cleanupFiles;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject||\Magento\Setup\Model\ObjectManagerProvider
     */
    private $objectManagerProvider;

    public function setup()
    {
        $this->backupRollbackFactory = $this->getMock(
            'Magento\Framework\Setup\BackupRollbackFactory',
            [],
            [],
            '',
            false
        );
        $this->backupRollback = $this->getMock('\Magento\Framework\Setup\BackupRollback', [], [], '', false);
        $this->status = $this->getMock('Magento\Setup\Model\Cron\Status', [], [], '', false);
        $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\OutputInterface', [], '', false);
        $this->objectManagerProvider = $this->getMock('Magento\Setup\Model\ObjectManagerProvider', [], [], '', false);
        $this->cleanupFiles = $this->getMock('Magento\Framework\App\State\CleanupFiles', [], [], '', false);
        $this->cache = $this->getMock('\Magento\Framework\App\Cache', [], [], '', false);
        $valueMap = [
            ['Magento\Framework\App\State\CleanupFiles', $this->cleanupFiles],
            ['Magento\Framework\App\Cache', $this->cache],
        ];

        $objectManager = $this->getMockForAbstractClass('Magento\Framework\ObjectManagerInterface', [], '', false);
        $this->objectManagerProvider->expects($this->once())->method('get')->willReturn($objectManager);
        $objectManager->expects($this->atLeastOnce())->method('get')->will($this->returnValueMap($valueMap));

        $this->jobDbRollback = new JobDbRollback(
            $this->backupRollbackFactory,
            $output,
            $this->status,
            $this->objectManagerProvider,
            'setup:rollback',
            ['backup_file_name' => 'someFileName']
        );
    }

    public function testExecute()
    {
        $this->backupRollbackFactory->expects($this->once())->method('create')->willReturn($this->backupRollback);
        $this->backupRollback->expects($this->once())->method('dbRollback');
        $this->jobDbRollback->execute();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not complete
     */
    public function testExceptionOnExecute()
    {
        $this->backupRollbackFactory->expects($this->once())->method('create')->willThrowException(new \Exception);
        $this->jobDbRollback->execute();
    }
}
