<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Analytics\Controller\Adminhtml\Subscription;

use Magento\Analytics\Model\Config\Backend\Enabled\SubscriptionHandler;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Retry subscription to Magento BI Advanced Reporting.
 */
class Retry extends Action
{
    /**
     * Resource for managing subscription to Magento Analytics.
     *
     * @var SubscriptionHandler
     */
    private $subscriptionHandler;

    /**
<<<<<<< HEAD
=======
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Magento_Analytics::analytics_settings';

    /**
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @param Context $context
     * @param SubscriptionHandler $subscriptionHandler
     */
    public function __construct(
        Context $context,
        SubscriptionHandler $subscriptionHandler
    ) {
        $this->subscriptionHandler = $subscriptionHandler;
        parent::__construct($context);
    }

    /**
<<<<<<< HEAD
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Analytics::analytics_settings');
    }

    /**
=======
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * Retry process of subscription.
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $resultRedirect->setPath('adminhtml');
            $this->subscriptionHandler->processEnabled();
        } catch (LocalizedException $e) {
            $this->getMessageManager()->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->getMessageManager()->addExceptionMessage(
                $e,
                __('Sorry, there has been an error processing your request. Please try again later.')
            );
        }

        return $resultRedirect;
    }
}
