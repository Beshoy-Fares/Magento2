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
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Framework\App\Area::PART_CONFIG);
$designDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';
$themeDir = $designDir . '/frontend/test_default';
$sourcePath = dirname(__DIR__) . '/Model/_files/design/frontend/test_publication/';

@mkdir($themeDir . '/images', 0777, true);

// Copy all files to fixture location
$mTime = time() - 10;
// To ensure that all files, changed later in test, will be recognized for publication
$files = array('theme.xml', 'style.css', 'sub.css', 'images/square.gif', 'images/rectangle.gif');
foreach ($files as $file) {
    copy($sourcePath . $file, $themeDir . '/' . $file);
    touch($themeDir . '/' . $file, $mTime);
}


$appInstallDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir();
\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
    array(
        \Magento\Framework\App\Filesystem::PARAM_APP_DIRS => array(
            \Magento\Framework\App\Filesystem::THEMES_DIR => array('path' => "{$appInstallDir}/media_for_change")
        )
    )
);

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Core\Model\Theme\Registration'
);
$registration->register('*/*/theme.xml');
