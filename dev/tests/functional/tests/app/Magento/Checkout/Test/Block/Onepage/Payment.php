<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Magento\Mtf\Block\Block;
use Magento\Payment\Test\Fixture\CreditCard;

/**
 * Checkout payment block.
 */
class Payment extends Block
{
    /**
     * Payment method input selector.
     *
     * @var string
     */
    protected $paymentMethodInput = '#p_method_%s';

    /**
     * Labels for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabels = '[for^=p_method_]';

    /**
     * Label for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabel = '[for=%s]';

    /**
     * Wait element.
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Purchase order number selector.
     *
     * @var string
     */
    protected $purchaseOrderNumber = '#po_number';

    /**
     * Selector for active payment method.
     *
     * @var string
     */
    protected $activePaymentMethodSelector = '.payment-method._active';

    /**
     * Select payment method.
     *
     * @param array $payment
     * @param CreditCard|null $creditCard
     * @throws \Exception
     * @return void
     */
    public function selectPaymentMethod(array $payment, CreditCard $creditCard = null)
    {
        $paymentSelector = $this->_rootElement->find(sprintf($this->paymentMethodInput, $payment['method']));
        if ($paymentSelector->isVisible()) {
            $paymentSelector->click();
        } else {
            $paymentSelector = $this->_rootElement->find(sprintf($this->paymentMethodLabel, $payment['method']));
            if (!$paymentSelector->isVisible()) {
                throw new \Exception('Such payment method is absent.');
            }
        }
        if ($payment['method'] == "purchaseorder") {
            $this->_rootElement->find($this->purchaseOrderNumber)->setValue($payment['po_number']);
        }
        if ($creditCard !== null) {
            /** @var \Magento\Payment\Test\Block\Form\Cc $formBlock */
            $formBlock = $this->blockFactory->create(
                '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
                ['element' => $this->_rootElement->find('#payment_form_' . $payment['method'])]
            );
            $formBlock->fill($creditCard);
        }
    }

    /**
     * Get selected payment method block.
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Payment\Method
     */
    public function getSelectedPaymentMethodBlock()
    {
        $element = $this->_rootElement->find($this->activePaymentMethodSelector);

        return $this->blockFactory->create(
            '\Magento\Checkout\Test\Block\Onepage\Payment\Method',
            ['element' => $element]
        );
    }
}
