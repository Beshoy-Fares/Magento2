<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

use Magento\Theme\Controller\Adminhtml\System\Design\Theme;

/**
 * Class Grid
 * @deprecated 100.2.0
 */
class Grid extends Theme
{
    /**
     * Grid ajax action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
