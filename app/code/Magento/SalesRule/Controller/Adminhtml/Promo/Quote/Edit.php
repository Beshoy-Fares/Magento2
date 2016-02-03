<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

class Edit extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Promo quote edit action
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

        $this->_coreRegistry->register('current_promo_quote_rule', $model);

        $resultPage = $this->resultPageFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('sales_rule/*');
                return;
            }

            $resultPage->getLayout()->getBlock('promo_sales_rule_edit_tab_coupons')->setCanShow(true);
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getConditions()->setFormName('sales_rule_form');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');
        $model->getActions()->setFormName('sales_rule_form');

        $this->_initAction();
        //$this->_view->getLayout()->getBlock('promo_quote_edit')->setData('action', $this->getUrl('sales_rule/*/save'));

        $this->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Cart Price Rule')
        );
        $this->_view->renderLayout();
    }
}
