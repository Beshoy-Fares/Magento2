<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Controller\Address;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;


/**
 * Delete customer address controller action.
 */
class Delete extends \Magento\Customer\Controller\Address implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @inheritdoc
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId && $this->_formKeyValidator->validate($this->getRequest())) {
            $customer = $this->customerRepository->getById($this->_getSession()->getCustomerId());
            $addresses = $customer->getAddresses();

            try {
                $addressesFiltered = array_filter($addresses, function ($customerAddress) use ($addressId){
                    return $customerAddress->getId() != $addressId;
                });
                if (count($addresses) !== count($addressesFiltered)) {
                    $customer->setAddresses($addressesFiltered);
                    $this->customerRepository->save($customer);
                    $this->messageManager->addSuccess(__('You deleted the address.'));
                } else {
                    $this->messageManager->addError(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t delete the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
