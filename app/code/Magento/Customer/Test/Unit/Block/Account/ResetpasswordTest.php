<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Block\Account;

/**
 * Test class for \Magento\Customer\Block\Account\Resetpassword
 */
class ResetpasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Helper\Config
     */
    protected $customerConfigHelperMock;

    /**
     * @var \Magento\Customer\Block\Account\Resetpassword
     */
    protected $block;

    /**
     * Init mocks for tests
     * @return void
     */
    public function setUp()
    {
        $this->customerConfigHelperMock =  $this->getMock(
            '\Magento\Customer\Helper\Config',
            [],
            [],
            '',
            false
        );

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->block = $objectManager->getObject(
            '\Magento\Customer\Block\Account\Resetpassword',
            ['customerConfigHelper' => $this->customerConfigHelperMock]
        );
    }

    /**
     * @return void
     */
    public function testGetMinimumPasswordLength()
    {
        $minimumPasswordLength = '8';

        $this->customerConfigHelperMock->expects($this->once())
            ->method('getMinimumPasswordLength')
            ->willReturn($minimumPasswordLength);

        $this->assertEquals($minimumPasswordLength, $this->block->getMinimumPasswordLength());
    }

    /**
     * @return void
     */
    public function testGetRequiredCharacterClassesNumber()
    {
        $requiredCharacterClassesNumber = '4';

        $this->customerConfigHelperMock->expects($this->once())
            ->method('getRequiredCharacterClassesNumber')
            ->willReturn($requiredCharacterClassesNumber);

        $this->assertEquals($requiredCharacterClassesNumber, $this->block->getRequiredCharacterClassesNumber());
    }
}
