<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Setup\Test\Page\Adminhtml\SetupWizard;

/**
 * Class AssertGrid
 */
class AssertModule extends AbstractConstraint
{
    public function processAssert(SetupWizard $setupWizard, $moduleName)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $setupWizard->getModuleGrid()->findModuleByName($moduleName)->isVisible(),
            'Module was not found in grid.'
        );
    }

    public function toString()
    {
        return 'module was found in grid.';
    }

}