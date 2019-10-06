<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\User\Controller\Adminhtml\Auth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\User\Controller\Adminhtml\Auth;

/**
 * Class ResetPassword
 */
class ResetPassword extends Auth implements HttpGetActionInterface
{
    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     *
     * @return void
     */
    public function execute()
    {
        $passwordResetToken = (string)$this->getRequest()->getQuery('token');
        $userId = (int)$this->getRequest()->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($userId, $passwordResetToken);

            $this->_view->loadLayout();

            $content = $this->_view->getLayout()->getBlock('content');
            if ($content) {
                $content->setData('user_id', $userId)->setData('reset_password_link_token', $passwordResetToken);
            }

            $this->_view->renderLayout();
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Your password reset link has expired.'));
            $this->_redirect('adminhtml/auth/forgotpassword', ['_nosecret' => true]);
            return;
        }
    }
}
