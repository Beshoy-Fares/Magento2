<?php
/**
 * Store configuration edit form
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Backend\Test\Block\System\Config;

use \Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Mtf\Factory\Factory;

class Form extends Block
{
    /**
     * Group block
     *
     * @var string
     */
    protected $groupBlock = '//legend[contains(text(), "%s")]/../..';

    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '//button[@data-ui-id="system-config-edit-save-button"]';

    /**
     * Retrieve store configuration form group
     *
     * @param string $name
     * @return Form\Group
     */
    public function getGroup($name)
    {
        $blockFactory = Factory::getBlockFactory();
        $element = $this->_rootElement->find(
            sprintf($this->groupBlock, $name), Locator::SELECTOR_XPATH
        );
        return $blockFactory->getMagentoBackendSystemConfigFormGroup($element);
    }

    /**
     * Save store configuration
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton, Locator::SELECTOR_XPATH)->click();
    }
}
