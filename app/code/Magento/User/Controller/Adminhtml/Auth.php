<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Controller\Adminhtml;

use Magento\Framework\Encryption\Helper\Security;
use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\User\Model\UserFactory;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

/**
 * \Magento\User Auth controller
 */
abstract class Auth extends AbstractAction
{
    /**
     * User model factory
     *
     * @var UserFactory
     */
    protected $_userFactory;
    
    /**
     * @var Data
     */
    protected $_backendDataHelper;

    /**
     * Construct
     *
     * @param Context $context
     * @param UserFactory $userFactory
     * @param Data $backendDataHelper
     */
    public function __construct(
        Context $context,
        UserFactory $userFactory,
        Data $backendDataHelper
    ) {
        parent::__construct($context);
        $this->_userFactory = $userFactory;
        $this->_backendDataHelper = $backendDataHelper;
    }

    /**
     * Check if password reset token is valid
     *
     * @param int $userId
     * @param string $resetPasswordToken
     * @return void
     * @throws LocalizedException
     */
    protected function _validateResetPasswordLinkToken($userId, $resetPasswordToken)
    {
        if (!is_int(
            $userId
        ) || !is_string(
            $resetPasswordToken
        ) || empty($resetPasswordToken) || empty($userId) || $userId < 0
        ) {
            throw new LocalizedException(
                __('The password reset token is incorrect. Verify the token and try again.')
            );
        }

        /** @var $user \Magento\User\Model\User */
        $user = $this->_userFactory->create()->load($userId);
        if (!$user->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please specify the correct account and try again.')
            );
        }

        $userToken = $user->getRpToken();
        if (!Security::compareStrings($userToken, $resetPasswordToken) || $user->isResetPasswordLinkTokenExpired()) {
            throw new LocalizedException(__('Your password reset link has expired.'));
        }
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
