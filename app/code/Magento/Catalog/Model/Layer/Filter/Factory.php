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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer filter factory
 */
namespace Magento\Catalog\Model\Layer\Filter;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create layer filter
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Layer\Filter\Attribute
     * @throws \Magento\Core\Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof \Magento\Catalog\Model\Layer\Filter\AbstractFilter) {
            throw new \Magento\Core\Exception($className
                . ' doesn\'t extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter');
        }
        return $filter;
    }
}
