<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Layout;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\DataObject;

/**
 * Page layout config model
 */
class Config
{
    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts;

    /**
     * @var DataInterface
     */
    protected $_dataStorage;

    /**
     * Constructor
     *
     * @param DataInterface $dataStorage
     */
    public function __construct(
        DataInterface $dataStorage
    ) {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Initialize page layouts list
     *
     * @return Config
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = [];
            foreach ($this->_dataStorage->get(null) as $layoutCode => $layoutConfig) {
                $layoutConfig['label'] = __($layoutConfig['label']);
                $this->_pageLayouts[$layoutCode] = new DataObject($layoutConfig);
            }
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return DataObject[]
     */
    public function getPageLayouts()
    {
        $this->_initPageLayouts();
        return $this->_pageLayouts;
    }

    /**
     * Retrieve page layout by code
     *
     * @param string $layoutCode
     * @return DataObject|boolean
     */
    public function getPageLayout($layoutCode)
    {
        $this->_initPageLayouts();

        if (isset($this->_pageLayouts[$layoutCode])) {
            return $this->_pageLayouts[$layoutCode];
        }

        return false;
    }

    /**
     * Retrieve page layout handles
     *
     * @return array
     */
    public function getPageLayoutHandles()
    {
        $handles = [];

        foreach ($this->getPageLayouts() as $layout) {
            $handles[$layout->getCode()] = $layout->getCode();
        }

        return $handles;
    }
}
