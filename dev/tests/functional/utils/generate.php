<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require_once dirname(__FILE__) . '/' . 'bootstrap.php';

// Generate page
$objectManager->create('Magento\Mtf\Util\Generate\Page')->launch();

// Generate fixtures
$magentoObjectManagerFactory = \Magento\Framework\App\Bootstrap::createObjectManagerFactory(BP, $_SERVER);
$magentoObjectManager = $magentoObjectManagerFactory->create($_SERVER);
$fieldsProvider = $magentoObjectManager->create('\Magento\Mtf\Util\Generate\Fixture\FieldsProvider');
$objectManager->create('Magento\Mtf\Util\Generate\Fixture', ['fieldsProvider' => $fieldsProvider])->launch();

// Generate repositories
$magentoObjectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
$collectionProvider = $magentoObjectManager->create('\Magento\Mtf\Util\Generate\Repository\CollectionProvider');
$objectManager->create('Magento\Mtf\Util\Generate\Repository', ['collectionProvider' => $collectionProvider])->launch();

// Generate factories for old end-to-end tests
$magentoObjectManager->create('Magento\Mtf\Util\Generate\Factory')->launch();

\Magento\Mtf\Util\Generate\GenerateResult::displayResults();
