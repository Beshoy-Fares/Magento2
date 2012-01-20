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
 * @category    Magento
 * @package     Magento_Admin
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests admin user model:
 * - general behaviour is tested
 *
 * @group module:Mage_Admin
 * @magentoDataFixture Mage/Admin/_files/user.php
 */
class Mage_Admin_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Admin_Model_User
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Admin_Model_User;
    }

    /**
     * Ensure that an exception is not thrown if the user does not exist
     */
    public function testloadByUsername() {
        $this->_model->loadByUsername('non_exiting_user');
        $this->assertNull($this->_model->getId(), 'The admin user has an unexpected ID');
        $this->_model->loadByUsername('adminuser');
        $this->assertTrue(!is_null($this->_model->getId()), 'The admin user should have been loaded');
    }
}
