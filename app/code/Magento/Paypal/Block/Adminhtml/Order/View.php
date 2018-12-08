<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Paypal\Block\Adminhtml\Order;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Paypal\Model\Adminhtml\Express;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Sales\Helper\Reorder;
use Magento\Sales\Model\Config;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;

/**
<<<<<<< HEAD
 * Adminhtml sales order view
=======
 * Adminhtml sales order view.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 * @api
 */
class View extends OrderView
{
    /**
     * @var Express
     */
    private $express;
<<<<<<< HEAD
=======

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
    /**
     * @param Context $context
     * @param Registry $registry
     * @param Config $salesConfig
     * @param Reorder $reorderHelper
     * @param Express $express
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Config $salesConfig,
        Reorder $reorderHelper,
        Express $express,
        array $data = []
    ) {
        $this->express = $express;

        parent::__construct(
            $context,
            $registry,
            $salesConfig,
            $reorderHelper,
            $data
        );
    }

    /**
<<<<<<< HEAD
     * Constructor
=======
     * Constructor.
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();

        $order = $this->getOrder();
<<<<<<< HEAD
        if (!$order) {
=======
        if ($order === null) {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            return;
        }
        $message = __('Are you sure you want to authorize full order amount?');
        if ($this->_isAllowedAction('Magento_Paypal::authorization') && $this->canAuthorize($order)) {
            $this->addButton(
                'order_authorize',
                [
                    'label' => __('Authorize'),
                    'class' => 'authorize',
<<<<<<< HEAD
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getPaymentAuthorizationUrl()}')"
=======
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getPaymentAuthorizationUrl()}')",
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                ]
            );
        }
    }

    /**
     * Returns URL for authorization of full order amount.
     *
     * @return string
     */
    private function getPaymentAuthorizationUrl(): string
    {
        return $this->getUrl('paypal/express/authorization');
    }

    /**
     * Checks if order available for payment authorization.
     *
     * @param Order $order
     * @return bool
     * @throws LocalizedException
     */
    public function canAuthorize(Order $order): bool
    {
        if ($order->canUnhold() || $order->isPaymentReview()) {
            return false;
        }

        $state = $order->getState();
        if ($order->isCanceled() || $state === Order::STATE_COMPLETE || $state === Order::STATE_CLOSED) {
            return false;
        }

        return $this->express->isOrderAuthorizationAllowed($order->getPayment());
    }
}
