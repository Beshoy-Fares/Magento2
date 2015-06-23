<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Test\Unit\Model\Theme\Plugin;

use Magento\Theme\Model\Theme\Plugin\Registration;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Theme\Model\Theme\Registration|\PHPUnit_Framework_MockObject_MockObject */
    protected $themeRegistration;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \Magento\Backend\App\AbstractAction|\PHPUnit_Framework_MockObject_MockObject */
    protected $abstractAction;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    public function setUp()
    {
        $this->themeRegistration = $this->getMock('Magento\Theme\Model\Theme\Registration', [], [], '', false);
        $this->logger = $this->getMockForAbstractClass('Psr\Log\LoggerInterface', [], '', false);
        $this->abstractAction = $this->getMockForAbstractClass('Magento\Backend\App\AbstractAction', [], '', false);
        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface', [], '', false);
    }

    public function testBeforeDispatch()
    {
        $this->themeRegistration->expects($this->once())->method('register');
        $this->logger->expects($this->never())->method('critical');
        $object = new Registration($this->themeRegistration, $this->logger);
        $object->beforeDispatch($this->abstractAction, $this->request);
    }

    public function testBeforeDispatchWithException()
    {
        $exception = new LocalizedException(new Phrase('Phrase'));
        $this->themeRegistration->expects($this->once())->method('register')->willThrowException($exception);
        $this->logger->expects($this->once())->method('critical');
        $object = new Registration($this->themeRegistration, $this->logger);
        $object->beforeDispatch($this->abstractAction, $this->request);
    }
}
