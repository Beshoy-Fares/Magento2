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
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_UrlrewriteControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Test presence of edit form
     */
    public function testEditActionIsFormPresent()
    {
        $this->dispatch('backend/admin/urlrewrite/edit/id');
        $response = $this->getResponse()->getBody();
        // Check that there is only one instance of edit_form
        $this->assertSelectCount('form#edit_form', 1, $response);
        // Check edit form attributes
        $saveUrl = Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/urlrewrite/save');
        $this->assertTag(array(
            'tag' => 'form',
            'attributes' => array(
                'id' => 'edit_form',
                'method' => 'post',
                'action' => $saveUrl
            )
        ), $response, 'Edit form does not contain all required attributes');
    }
}
