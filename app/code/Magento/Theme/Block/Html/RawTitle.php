<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Block\Html;

use Magento\Framework\View\Element\Template;

/**
 * Html page raw title block
 *
 * @method $this setTitleId($titleId)
 * @method $this setTitleClass($titleClass)
 * @method string getTitleId()
 * @method string getTitleClass()
 */
class RawTitle extends Template
{
    /**
     * Own page title to display on the page
     *
     * @var string
     */
    private $pageTitle;

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }
        return $this->pageConfig->getTitle()->getShort();
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }
        return $this->pageConfig->getTitle()->getShortHeading();
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }
}
