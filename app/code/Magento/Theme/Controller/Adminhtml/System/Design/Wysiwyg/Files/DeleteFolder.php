<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files;

use Exception;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files;

class DeleteFolder extends Files
{
    /**
     * Delete folder action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $path = $this->storage->getCurrentPath();
            $this->_getStorage()->deleteDirectory($path);
        } catch (Exception $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $this->getResponse()->representJson(
                $this->_objectManager->get(JsonHelper::class)->jsonEncode($result)
            );
        }
    }
}
