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

namespace Magento\Framework\App\PageCache;

/**
 * Class FormKeyTest
 * @package Magento\Framework\App\PageCache
 */
class FormKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var FormKey
     */
    protected $formKey;

    /**
     * Cookie mock
     *
     * @var \Magento\Framework\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMock;

    /**
     * Create cookie mock and FormKey instance
     */
    public function setUp()
    {
        $this->cookieMock = $this->getMock('Magento\Framework\Stdlib\Cookie', array('get'), array(), '', false);
        $this->formKey =  new \Magento\Framework\App\PageCache\FormKey($this->cookieMock);
    }

    public function testGet()
    {
        //Data
        $formKey = 'test from key';

        //Verification
        $this->cookieMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Framework\App\PageCache\FormKey::COOKIE_NAME)
            ->will($this->returnValue($formKey));

        $this->assertEquals($formKey, $this->formKey->get());
    }
}
