<?php
/**
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

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Register
 * Register new customer on Frontend
 *
 */
class Register extends Form
{
    /**
     * 'Submit' form button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Create new customer account and fill billing address if it exists
     *
     * @param FixtureInterface $fixture
     * @param $address
     */
    public function registerCustomer(FixtureInterface $fixture, $address = null)
    {
        $this->fill($fixture);
        if ($address !== null) {
            $this->fill($address);
        }
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
