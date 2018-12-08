<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Config\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Config\Test\Page\Adminhtml\AdminAccountSharing;

/**
 * Steps:
 * 1. Log in to Admin.
 * 2. Go to Stores>Configuration>Advanced>admin>Security.
<<<<<<< HEAD
 * 3. Verify admin Account Sharing option availability.
=======
 * 3. * 7. Verify admin Account Sharing option availability.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 *
 * @group Config_(PS)
 * @ZephyrId MAGETWO-47822
 */
class VerifyAdminAccountSharingEntityTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const DOMAIN = 'PS';
    const TO_MAINTAIN = 'yes';
    const TEST_TYPE = 'extended_acceptance_test';
    /* end tags */

    /**
     * Admin account settings page.
     *
     * @var AdminAccountSharing
     */
    private $adminAccountSharing;

    /**
     * @param AdminAccountSharing $adminAccountSharing
     */
    public function __inject(
        AdminAccountSharing $adminAccountSharing
    ) {
        $this->adminAccountSharing = $adminAccountSharing;
    }

    /**
     * Create Verify Admin Account Sharing test.
     *
     * @return void
     */
    public function test()
    {
        $this->adminAccountSharing->open();
        sleep(10);
    }
}
