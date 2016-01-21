<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\User\Controller\Adminhtml\User\Role;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Controller\ResultFactory;

class SaveRole extends \Magento\User\Controller\Adminhtml\User\Role
{
    /**
     * Session keys for Info form data
     */
    const ROLE_EDIT_FORM_DATA_SESSION_KEY = 'role_edit_form_data';

    /**
     * Session keys for Users form data
     */
    const IN_ROLE_USER_FORM_DATA_SESSION_KEY = 'in_role_user_form_data';

    /**
     * Session keys for original Users form data
     */
    const IN_ROLE_OLD_USER_FORM_DATA_SESSION_KEY = 'in_role_old_user_form_data';

    /**
     * Session keys for Use all resources flag form data
     */
    const RESOURCE_ALL_FORM_DATA_SESSION_KEY = 'resource_all_form_data';

    /**
     * Session keys for Resource form data
     */
    const RESOURCE_FORM_DATA_SESSION_KEY = 'resource_form_data';

    /**
     * Assign user to role
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    protected function _addUserToRole($userId, $roleId)
    {
        $user = $this->_userFactory->create()->load($userId);
        $user->setRoleId($roleId);

        if ($user->roleUserExists() === true) {
            return false;
        } else {
            $user->save();
            return true;
        }
    }

    /**
     * Remove user from role
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     * @throws \Exception
     */
    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            $this->_userFactory->create()->setRoleId($roleId)->setUserId($userId)->deleteFromRole();
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * Role form submit action to save or create new role
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $rid = $this->getRequest()->getParam('role_id', false);
        $resource = $this->getRequest()->getParam('resource', false);
        $roleUsers = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = [$this->_objectManager->get('Magento\Framework\Acl\RootResource')->getId()];
        }

        $role = $this->_initRole('role_id');
        if (!$role->getId() && $rid) {
            $this->messageManager->addError(__('This role no longer exists.'));
            return $resultRedirect->setPath('adminhtml/*/');
        }

        $password = $this->getRequest()->getParam(
            \Magento\User\Block\Role\Tab\Info::IDENTITY_VERIFICATION_PASSWORD_FIELD
        );
        if (!$this->securityCheck($password)) {
            return $this->saveDataToSessionAndRedirect($role, $this->getRequest()->getPostValue(), $resultRedirect);
        }

        try {
            $roleName = $this->_filterManager->removeTags($this->getRequest()->getParam('rolename', false));
            $role->setName($roleName)
                ->setPid($this->getRequest()->getParam('parent_id', false))
                ->setRoleType(RoleGroup::ROLE_TYPE)
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
            $this->_eventManager->dispatch(
                'admin_permissions_role_prepare_save',
                ['object' => $role, 'request' => $this->getRequest()]
            );
            $role->save();

            $this->_rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();

            foreach ($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }
            $this->messageManager->addSuccess(__('You saved the role.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while saving this role.'));
        }

        return $resultRedirect->setPath('adminhtml/*/');
    }

    /**
     * @param string $passwordString
     * @return bool
     */
    protected function securityCheck($passwordString)
    {
        $isCheckSuccessful = true;
        if (!$this->performIdentityCheck($passwordString)) {
            $this->messageManager->addError(__('You have entered an invalid password for current user.'));
            $isCheckSuccessful = false;
        }

        return $isCheckSuccessful;
    }

    /**
     * @param string $passwordString
     * @return bool
     */
    protected function performIdentityCheck($passwordString)
    {
        try {
            $result = $this->_authSession->getUser()->verifyIdentity($passwordString);
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param \Magento\Authorization\Model\Role $role
     * @param array $data
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function saveDataToSessionAndRedirect($role, $data, $resultRedirect)
    {
        $this->_getSession()->setData(self::ROLE_EDIT_FORM_DATA_SESSION_KEY, ['rolename' => $data['rolename']]);
        $this->_getSession()->setData(self::IN_ROLE_USER_FORM_DATA_SESSION_KEY, $data['in_role_user']);
        $this->_getSession()->setData(self::IN_ROLE_OLD_USER_FORM_DATA_SESSION_KEY, $data['in_role_user_old']);
        if ($data['all']) {
            $this->_getSession()->setData(self::RESOURCE_ALL_FORM_DATA_SESSION_KEY, $data['all']);
        } else {
            $resource = isset($data['resource']) ? $data['resource'] : [];
            $this->_getSession()->setData(self::RESOURCE_FORM_DATA_SESSION_KEY, $resource);
        }
        $arguments = $role->getId() ? ['rid' => $role->getId()] : [];
        return $resultRedirect->setPath('adminhtml/*/editrole', $arguments);
    }
}
