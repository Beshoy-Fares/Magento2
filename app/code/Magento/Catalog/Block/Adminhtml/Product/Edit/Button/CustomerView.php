<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Action\UrlBuilder;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic as CoreGeneric;

/**
 * Class CustomerView
 *
 * @author      Marcin Dykas <mdykas@divante.pl>
 */
class CustomerView extends CoreGeneric
{
    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var \Magento\Framework\Url */
    protected $urlHelper;

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    public function __construct(
        Context $context,
        Registry $registry,
        UrlBuilder $actionUrlBuilder
    )
    {
        $this->context = $context;
        $this->registry = $registry;
        $this->actionUrlBuilder = $actionUrlBuilder;

        $this->product = $this->getProduct();

        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $buttonData = [
            'label' => __('Customer View'),
            'on_click' => sprintf("window.open('%s', '_blank');", $this->getCustomerViewUrl()),
            'class' => 'action-secondary',
        ];

        if (!$this->product->isSalable() || !$this->product->getId()) {
            $buttonData['disabled'] = 'disabled';
        }

        return $buttonData;
    }

    /**
     * @return string
     */
    public function getCustomerViewUrl()
    {
        /* @var \Magento\Store\Model\Store\Interceptor */
        $currentStore = $this->registry->registry('current_store');

        $scope = $currentStore->getStoreId();
        $store = $currentStore->getCode();

        return $this->actionUrlBuilder->getUrl(
            'catalog/product/view',
            $this->product,
            $scope,
            $store
        );
    }
}
