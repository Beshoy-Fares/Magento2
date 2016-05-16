<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Section\RuleInformation;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteNew;

/**
 * Class AssertCustomerGroupNotOnCartPriceRuleForm.
 */
class AssertCustomerGroupNotOnCartPriceRuleForm extends AbstractConstraint
{
    /**
     * Assert that customer group is not on cart price rule page.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteNew $promoQuoteNew
     * @param CustomerGroup $customerGroup
     * @return void
     */
    public function processAssert(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteNew $promoQuoteNew,
        CustomerGroup $customerGroup
    ) {
        $promoQuoteIndex->open();
        $promoQuoteIndex->getGridPageActions()->addNew();
        $promoQuoteNew->getSalesRuleForm()->openSection('rule_information');

        /** @var RuleInformation $ruleInformationTab */
        $ruleInformationTab = $promoQuoteNew->getSalesRuleForm()->getSection('rule_information');
        \PHPUnit_Framework_Assert::assertFalse(
            $ruleInformationTab->isVisibleCustomerGroup($customerGroup),
            "Customer group {$customerGroup->getCustomerGroupCode()} is still in cart price rule page."
        );
    }

    /**
     * Success assert of customer group not on cart price rule page.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group is not on cart price rule page.';
    }
}
