<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Creditmemo;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Credit memo view action
 */
class View extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_creditmemo';

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    
    /**
     * @param Action\Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
    	\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Creditmemo information page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
        $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
        $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('sales_creditmemo_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $resultPage->setActiveMenu('Magento_Sales::sales_creditmemo');
            if ($creditmemo->getInvoice()) {
                $resultPage->getConfig()->getTitle()->prepend(
                    __("View Memo for #%1", $creditmemo->getInvoice()->getIncrementId())
                );
            } else {
                $resultPage->getConfig()->getTitle()->prepend(__("View Memo"));
            }
            return $resultPage;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('sales/creditmemo');
    }
}

