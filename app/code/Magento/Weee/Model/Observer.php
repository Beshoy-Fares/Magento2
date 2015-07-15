<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Weee\Model;

use Magento\Weee\Model\Tax as WeeeDisplayConfig;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Observer extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_productType;

    /**
     * Weee data
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $_weeeData = null;

    /**
     * @var Tax
     */
    protected $_weeeTax;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Tax $weeeTax
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Model\Product\Type $productType
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\LayoutInterface $layout,
        Tax $weeeTax,
        \Magento\Weee\Helper\Data $weeeData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_layout = $layout;
        $this->_weeeTax = $weeeTax;
        $this->_taxData = $taxData;
        $this->_productType = $productType;
        $this->_weeeData = $weeeData;
        $this->productTypeConfig = $productTypeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function setWeeeRendererInForm(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getEvent()->getForm();

        $attributes = $this->_weeeTax->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            $weeeTax = $form->getElement($code);
            if ($weeeTax) {
                $weeeTax->setRenderer($this->_layout->createBlock('Magento\Weee\Block\Renderer\Weee\Tax'));
            }
        }

        return $this;
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function updateExcludedFieldList(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $attributes = $this->_weeeTax->getWeeeAttributeCodes(true);
        $list = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    /**
     * Get empty select object
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getSelect()
    {
        return $this->_weeeTax->getResource()->getReadConnection()->select();
    }

    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function addWeeeTaxAttributeType(\Magento\Framework\Event\Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = [
            'value' => 'weee',
            'label' => __('Fixed Product Tax'),
            'hide_fields' => [
                'is_unique',
                'is_required',
                'frontend_class',
                '_scope',
                '_default_value',
                '_front_fieldset',
            ],
        ];

        $response->setTypes($types);

        return $this;
    }

    /**
     * Automaticaly assign backend model to weee attributes
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function assignBackendModelToAttribute(\Magento\Framework\Event\Observer $observer)
    {
        $backendModel = \Magento\Weee\Model\Attribute\Backend\Weee\Tax::getBackendModelName();
        /** @var $object \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = [];
                foreach ($this->_productType->getOptions() as $option) {
                    if ($this->productTypeConfig->isProductSet($option['value'])) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }

    /**
     * Add custom element type for attributes form
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function updateElementTypes(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types['weee'] = 'Magento\Weee\Block\Element\Weee\Tax';
        $response->setTypes($types);
        return $this;
    }

    /**
     * Modify the options config for the front end to resemble the weee final price
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getPriceConfiguration(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_weeeData->isEnabled()) {
            $priceConfigObj=$observer->getData('configObj');
            try {
                $calcPrice = 'finalPrice';
                if ($this->_taxData->priceIncludesTax() &&
                    $this->_taxData->displayPriceExcludingTax()
                ) {
                    $calcPrice = 'basePrice';
                }
                $priceConfig = $this->recurConfigAndInsertWeeePrice($priceConfigObj->getConfig(), 'prices', $calcPrice);
                $priceConfigObj->setConfig($priceConfig);
            } catch (\Exception $e) {
                return $this;
            }
        }
        return $this;
    }

    /**
     * Recur through the config array and insert the wee price
     *
     * @param   array $input
     * @param   string $searchKey
     * @param   string $calcPrice
     * @return  array
     */
    private function recurConfigAndInsertWeeePrice($input, $searchKey, $calcPrice)
    {
        $holder = array();
        if (is_array($input)) {
            foreach ($input as $key => $el) {
                if (is_array($el)) {
                    $holder[$key] = $this->recurConfigAndInsertWeeePrice($el, $searchKey, $calcPrice);
                    if ($key === $searchKey) {
                        if ((!array_key_exists('weeePrice', $holder[$key])) &&
                        (array_key_exists($calcPrice, $holder[$key]))
                        ) {
                            $holder[$key]['weeePrice'] = $holder[$key][$calcPrice];

                            // only do processing on product options
                            if (array_key_exists('optionId', $input)) {
                                $product = $this->_registry->registry('current_product');
                                $typeInstance = $product->getTypeInstance();
                                if ($typeInstance instanceof \Magento\Bundle\Model\Product\Type) {
                                    $typeInstance->setStoreFilter($product->getStoreId(), $product);
                                    $selectionCollection = $typeInstance->getSelectionsCollection(
                                        $typeInstance->getOptionsIds($product),
                                        $product
                                    );

                                    foreach ($selectionCollection as $selectionItem) {
                                        if ($selectionItem->getId() == $input['optionId']) {
                                            $weeAttributes = $this->_weeeTax->getProductWeeeAttributes($selectionItem);
                                            foreach ($weeAttributes as $weeAttribute) {
                                                $holder[$key]['weeePrice' . $weeAttribute->getCode()] =
                                                    ['amount' => $weeAttribute->getAmount()];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $holder[$key] = $el;
                }
            }
        }
        return $holder;
    }

    /**
     * Change default JavaScript templates for options rendering
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function updateProductOptions(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getEvent()->getResponseObject();
        $options = $response->getAdditionalOptions();

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_registry->registry('current_product');
        if (!$product) {
            return $this;
        }

        if ($this->_weeeData->isEnabled() &&
            !$this->geDisplayIncl($product->getStoreId()) &&
            !$this->geDisplayExcl($product->getStoreId())
        ) {
            $typeInstance = $product->getTypeInstance();
            // only do processing on bundle product
            if ($typeInstance instanceof \Magento\Bundle\Model\Product\Type) {
                $typeInstance->setStoreFilter($product->getStoreId(), $product);

                $selectionCollection = $typeInstance->getSelectionsCollection(
                    $typeInstance->getOptionsIds($product),
                    $product
                );

                if (!array_key_exists('optionTemplate', $options)) {
                    $options['optionTemplate'] = '<%- data.label %>'
                        . '<% if (data.finalPrice.value) { %>'
                        . ' +<%- data.finalPrice.formatted %>'
                        . '<% } %>';
                }

                $insertedWeeCodesArray = [];
                foreach ($selectionCollection as $selectionItem) {
                    $weeAttributes = $this->_weeeTax->getProductWeeeAttributes($selectionItem);
                    foreach ($weeAttributes as $weeAttribute) {
                        if (!array_key_exists($weeAttribute->getCode(), $insertedWeeCodesArray)) {
                            $options['optionTemplate'] .= sprintf(
                                ' <%% if (data.weeePrice' . $weeAttribute->getCode() . ') { %%>'
                                . '  ('
                                . $weeAttribute->getName()
                                . ':<%%= data.weeePrice' . $weeAttribute->getCode()
                                . '.formatted %%>)'
                                . '<%% } %%>'
                            );
                            $insertedWeeCodesArray[$weeAttribute->getCode()] = $weeAttribute->getCode();
                        }
                    }
                }

                if ($this->geDisplayExlDescIncl($product->getStoreId())) {
                    $options['optionTemplate'] .= sprintf(
                        ' <%% if (data.weeePrice) { %%>'
                        . '<%%= data.weeePrice.formatted %%>'
                        . '<%% } %%>'
                    );
                }

            }
        }

        $response->setAdditionalOptions($options);
        return $this;
    }

    private function geDisplayIncl($storeId = null)
    {
        return $this->_weeeData->typeOfDisplay(
            WeeeDisplayConfig::DISPLAY_INCL,
            \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW,
            $storeId
        );
    }

    private function geDisplayExlDescIncl($storeId = null)
    {
        return $this->_weeeData->typeOfDisplay(
            WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL,
            \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW,
            $storeId
        );
    }
    private function geDisplayExcl($storeId = null)
    {
        return $this->_weeeData->typeOfDisplay(
            WeeeDisplayConfig::DISPLAY_EXCL,
            \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW,
            $storeId
        );
    }
}
