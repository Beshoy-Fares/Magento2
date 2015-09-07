<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Component\Grid\Column;

use Magento\Ui\Component\Grid\Column\ValidationRules;
use Magento\Customer\Api\Data\ValidationRuleInterface;

class ValidationRulesTest extends \PHPUnit_Framework_TestCase
{
    /** @var ValidationRules */
    protected $validationRules;

    /** @var ValidationRuleInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $validationRule;

    protected function setUp()
    {
        $this->validationRules = $this->getMockBuilder('Magento\Ui\Component\Grid\Column\ValidationRules')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validationRule = $this->getMockBuilder('Magento\Customer\Api\Data\ValidationRuleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validationRules = new ValidationRules();
    }

    public function testGetValidationRules()
    {
        $expectsRules = [
            'required-entry' => true,
            'validate-number' => true,
        ];
        $this->validationRule->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('input_validation');
        $this->validationRule->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn('numeric');

        $this->assertEquals(
            $expectsRules,
            $this->validationRules->getValidationRules(
                true,
                [
                    $this->validationRule,
                    new \Magento\Framework\DataObject(),
                ]
            )
        );
    }

    public function testGetValidationRulesWithOnlyRequiredRule()
    {
        $expectsRules = [
            'required-entry' => true,
        ];
        $this->assertEquals(
            $expectsRules,
            $this->validationRules->getValidationRules(true, [])
        );
    }
}
