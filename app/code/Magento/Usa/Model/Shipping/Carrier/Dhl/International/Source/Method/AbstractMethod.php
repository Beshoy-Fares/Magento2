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
 * @package     Magento_Usa
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for DHL shipping methods
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method;

abstract class AbstractMethod extends \Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method\Generic
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string $_contentType
     */
    protected $_contentType;

    /**
     * Show 'none' in methods list or not;
     *
     * @var bool
     */
    protected $_noneMethod = false;

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        /* @var $carrierModel \Magento\Usa\Model\Shipping\Carrier\Dhl\International */
        $carrierModel   = $this->_shippingDhlInt;
        $dhlProducts    = $carrierModel->getDhlProducts($this->_contentType);

        $options = array();
        foreach ($dhlProducts as $code => $title) {
            $options[] = array('value' => $code, 'label' => $title);
        }

        if ($this->_noneMethod) {
            array_unshift($options, array('value' => '', 'label' => __('None')));
        }

        return $options;
    }
}
