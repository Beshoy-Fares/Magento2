<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require_once dirname(__DIR__) . '/' . 'bootstrap.php';

$magentoObjectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
$collectionProvider = $magentoObjectManager->create('\Magento\Mtf\Util\Generate\Repository\CollectionProvider');
$objectManager->create('Magento\Mtf\Util\Generate\Repository', ['collectionProvider' => $collectionProvider])->launch();
