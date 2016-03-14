<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Console\Command\Module;

/**
 * Command for enabling list or all of modules
 */
class EnableCommand extends AbstractModuleManageCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('module:enable')
            ->setDescription('Enables specified modules');
        parent::configure();
    }

    /**
     * Enable modules
     *
     * @return bool
     */
    protected function isEnable()
    {
        return true;
    }
}
