<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

class MassRemoveTest extends \Magento\Backend\Utility\BackendAclAbstractTest
{
    public function setUp()
    {
        $this->resource = 'Magento_AdminNotification::adminnotification_remove';
        $this->uri = 'backend/admin/notification/massremove';
        parent::setUp();
    }
}
