<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Analytics\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;

/**
 * A backend model for verticals configuration.
 */
class Vertical extends \Magento\Framework\App\Config\Value
{
    /**
     * Handles the value of the selected vertical before saving.
     *
     * Note that the selected vertical should not be empty since
     * it will cause distortion of the analytics reports.
     *
     * @return $this
     * @throws LocalizedException if the value of the selected vertical is empty.
     */
    public function beforeSave()
    {
        if (empty($this->getValue())) {
<<<<<<< HEAD
            throw new LocalizedException(__('Please select a vertical.'));
=======
            throw new LocalizedException(__('Please select an industry.'));
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
        }

        return $this;
    }
}
