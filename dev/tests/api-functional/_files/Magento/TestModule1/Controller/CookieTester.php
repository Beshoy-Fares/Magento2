<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestModule1\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieMonster;

/**
 * Controller for testing the CookieManager.
 *
 */
abstract class CookieTester implements \Magento\Framework\App\ActionInterface
{
    /** @var PhpCookieMonster */
    protected $cookieManager;

    /** @var  CookieMetadataFactory */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var
     */
    protected $request;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PhpCookieMonster $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PhpCookieMonster $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFacory = $cookieMetadataFactory;
        $this->_response = $context->getResponse();
        $this->request = $context->getRequest();
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieMetadataFactory()
    {
        return $this->cookieMetadataFacory;
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieManager()
    {
        return $this->cookieManager;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->request = $request;
        $result = $this->execute();
        return $result ? $result : $this->_response;
    }
}
