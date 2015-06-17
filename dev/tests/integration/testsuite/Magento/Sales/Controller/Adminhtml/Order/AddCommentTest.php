<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

class AddCommentTest extends \Magento\Backend\Utility\BackendAclAbstractTest
{
    public function setUp()
    {
        $this->resource = 'Magento_Sales::comment';
        $this->uri = 'backend/sales/order/addcomment';
        parent::setUp();
    }
}
