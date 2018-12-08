<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CheckoutAgreements\Controller\Adminhtml\Agreement;

use Magento\CheckoutAgreements\Controller\Adminhtml\Agreement;
use Magento\CheckoutAgreements\Model\AgreementFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
<<<<<<< HEAD

class Save extends Agreement
=======
use Magento\Framework\App\Action\HttpPostActionInterface;

class Save extends Agreement implements HttpPostActionInterface
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
{
    /**
     * @var AgreementFactory
     */
    private $agreementFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AgreementFactory $agreementFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AgreementFactory $agreementFactory = null
    ) {
        $this->agreementFactory = $agreementFactory ?:
                ObjectManager::getInstance()->get(AgreementFactory::class);
        parent::__construct($context, $coreRegistry);
    }
    /**
     * @return void
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            $model = $this->agreementFactory->create();
            $model->setData($postData);

            try {
                $validationResult = $model->validateData(new DataObject($postData));
                if ($validationResult !== true) {
                    foreach ($validationResult as $message) {
                        $this->messageManager->addError($message);
                    }
                } else {
                    $model->save();
                    $this->messageManager->addSuccess(__('You saved the condition.'));
                    $this->_redirect('checkout/*/');
                    return;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving this condition.'));
            }

            $this->_session->setAgreementData($postData);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
        }
    }
}
