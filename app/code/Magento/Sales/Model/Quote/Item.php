<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Quote Item Model
 *
 * @method \Magento\Sales\Model\Resource\Quote\Item _getResource()
 * @method \Magento\Sales\Model\Resource\Quote\Item getResource()
 * @method int getQuoteId()
 * @method \Magento\Sales\Model\Quote\Item setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Quote\Item setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Quote\Item setUpdatedAt(string $value)
 * @method int getProductId()
 * @method \Magento\Sales\Model\Quote\Item setProductId(int $value)
 * @method int getStoreId()
 * @method \Magento\Sales\Model\Quote\Item setStoreId(int $value)
 * @method int getParentItemId()
 * @method \Magento\Sales\Model\Quote\Item setParentItemId(int $value)
 * @method int getIsVirtual()
 * @method \Magento\Sales\Model\Quote\Item setIsVirtual(int $value)
 * @method string getSku()
 * @method \Magento\Sales\Model\Quote\Item setSku(string $value)
 * @method string getName()
 * @method \Magento\Sales\Model\Quote\Item setName(string $value)
 * @method string getDescription()
 * @method \Magento\Sales\Model\Quote\Item setDescription(string $value)
 * @method string getAppliedRuleIds()
 * @method \Magento\Sales\Model\Quote\Item setAppliedRuleIds(string $value)
 * @method string getAdditionalData()
 * @method \Magento\Sales\Model\Quote\Item setAdditionalData(string $value)
 * @method int getFreeShipping()
 * @method \Magento\Sales\Model\Quote\Item setFreeShipping(int $value)
 * @method int getIsQtyDecimal()
 * @method \Magento\Sales\Model\Quote\Item setIsQtyDecimal(int $value)
 * @method int getNoDiscount()
 * @method \Magento\Sales\Model\Quote\Item setNoDiscount(int $value)
 * @method float getWeight()
 * @method \Magento\Sales\Model\Quote\Item setWeight(float $value)
 * @method float getBasePrice()
 * @method \Magento\Sales\Model\Quote\Item setBasePrice(float $value)
 * @method float getCustomPrice()
 * @method float getDiscountPercent()
 * @method \Magento\Sales\Model\Quote\Item setDiscountPercent(float $value)
 * @method float getDiscountAmount()
 * @method \Magento\Sales\Model\Quote\Item setDiscountAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method \Magento\Sales\Model\Quote\Item setBaseDiscountAmount(float $value)
 * @method float getTaxPercent()
 * @method \Magento\Sales\Model\Quote\Item setTaxPercent(float $value)
 * @method \Magento\Sales\Model\Quote\Item setTaxAmount(float $value)
 * @method \Magento\Sales\Model\Quote\Item setBaseTaxAmount(float $value)
 * @method float getRowTotal()
 * @method \Magento\Sales\Model\Quote\Item setRowTotal(float $value)
 * @method float getBaseRowTotal()
 * @method \Magento\Sales\Model\Quote\Item setBaseRowTotal(float $value)
 * @method float getRowTotalWithDiscount()
 * @method \Magento\Sales\Model\Quote\Item setRowTotalWithDiscount(float $value)
 * @method float getRowWeight()
 * @method \Magento\Sales\Model\Quote\Item setRowWeight(float $value)
 * @method \Magento\Sales\Model\Quote\Item setProductType(string $value)
 * @method float getBaseTaxBeforeDiscount()
 * @method \Magento\Sales\Model\Quote\Item setBaseTaxBeforeDiscount(float $value)
 * @method float getTaxBeforeDiscount()
 * @method \Magento\Sales\Model\Quote\Item setTaxBeforeDiscount(float $value)
 * @method float getOriginalCustomPrice()
 * @method \Magento\Sales\Model\Quote\Item setOriginalCustomPrice(float $value)
 * @method string getRedirectUrl()
 * @method \Magento\Sales\Model\Quote\Item setRedirectUrl(string $value)
 * @method float getBaseCost()
 * @method \Magento\Sales\Model\Quote\Item setBaseCost(float $value)
 * @method float getPriceInclTax()
 * @method \Magento\Sales\Model\Quote\Item setPriceInclTax(float $value)
 * @method float getBasePriceInclTax()
 * @method \Magento\Sales\Model\Quote\Item setBasePriceInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method \Magento\Sales\Model\Quote\Item setRowTotalInclTax(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method \Magento\Sales\Model\Quote\Item setBaseRowTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method \Magento\Sales\Model\Quote\Item setGiftMessageId(int $value)
 * @method string getWeeeTaxApplied()
 * @method \Magento\Sales\Model\Quote\Item setWeeeTaxApplied(string $value)
 * @method float getWeeeTaxAppliedAmount()
 * @method \Magento\Sales\Model\Quote\Item setWeeeTaxAppliedAmount(float $value)
 * @method float getWeeeTaxAppliedRowAmount()
 * @method \Magento\Sales\Model\Quote\Item setWeeeTaxAppliedRowAmount(float $value)
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method \Magento\Sales\Model\Quote\Item setBaseWeeeTaxAppliedAmount(float $value)
 * @method float getBaseWeeeTaxAppliedRowAmnt()
 * @method \Magento\Sales\Model\Quote\Item setBaseWeeeTaxAppliedRowAmnt(float $value)
 * @method float getWeeeTaxDisposition()
 * @method \Magento\Sales\Model\Quote\Item setWeeeTaxDisposition(float $value)
 * @method float getWeeeTaxRowDisposition()
 * @method \Magento\Sales\Model\Quote\Item setWeeeTaxRowDisposition(float $value)
 * @method float getBaseWeeeTaxDisposition()
 * @method \Magento\Sales\Model\Quote\Item setBaseWeeeTaxDisposition(float $value)
 * @method float getBaseWeeeTaxRowDisposition()
 * @method \Magento\Sales\Model\Quote\Item setBaseWeeeTaxRowDisposition(float $value)
 * @method float getHiddenTaxAmount()
 * @method \Magento\Sales\Model\Quote\Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method \Magento\Sales\Model\Quote\Item setBaseHiddenTaxAmount(float $value)
 * @method null|bool getHasConfigurationUnavailableError()
 * @method \Magento\Sales\Model\Quote\Item setHasConfigurationUnavailableError(bool $value)
 * @method \Magento\Sales\Model\Quote\Item unsHasConfigurationUnavailableError()
 */
namespace Magento\Sales\Model\Quote;

class Item extends \Magento\Sales\Model\Quote\Item\AbstractItem
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_quote_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Quote model object
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * Item options array
     *
     * @var array
     */
    protected $_options             = array();

    /**
     * Item options by code cache
     *
     * @var array
     */
    protected $_optionsByCode       = array();

    /**
     * Not Represent options
     *
     * @var array
     */
    protected $_notRepresentOptions = array('info_buyRequest');

    /**
     * Flag stating that options were successfully saved
     *
     */
    protected $_flagOptionsSaved = null;

    /**
     * Array of errors associated with this quote item
     *
     * @var \Magento\Sales\Model\Status\ListStatus
     */
    protected $_errorInfos;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Sales\Model\Quote\Item\OptionFactory
     */
    protected $_itemOptionFactory;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Sales\Model\Status\ListFactory $statusListFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Sales\Model\Quote\Item\OptionFactory $itemOptionFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Sales\Model\Quote\Item\OptionFactory $itemOptionFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_errorInfos = $statusListFactory->create();
        $this->_locale = $locale;
        $this->_itemOptionFactory = $itemOptionFactory;
        parent::__construct($context, $registry, $productFactory, $resource, $resourceCollection, $data);
    }


    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Quote\Item');
    }

    /**
     * Quote Item Before Save prepare data process
     *
     * @return \Magento\Sales\Model\Quote\Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setIsVirtual($this->getProduct()->getIsVirtual());
        if ($this->getQuote()) {
            $this->setQuoteId($this->getQuote()->getId());
        }
        return $this;
    }

    /**
     * Retrieve address model
     *
     * @return \Magento\Sales\Model\Quote\Address
     */
    public function getAddress()
    {
        if ($this->getQuote()->getItemsQty() == $this->getQuote()->getVirtualItemsQty()) {
            $address = $this->getQuote()->getBillingAddress();
        } else {
            $address = $this->getQuote()->getShippingAddress();
        }

        return $address;
    }

    /**
     * Declare quote model object
     *
     * @param   \Magento\Sales\Model\Quote $quote
     * @return  \Magento\Sales\Model\Quote\Item
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->_quote = $quote;
        $this->setQuoteId($quote->getId());
        return $this;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Prepare quantity
     *
     * @param float|int $qty
     * @return int|float
     */
    protected function _prepareQty($qty)
    {
        $qty = $this->_locale->getNumber($qty);
        $qty = ($qty > 0) ? $qty : 1;
        return $qty;
    }

    /**
     * Adding quantity to quote item
     *
     * @param float $qty
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function addQty($qty)
    {
        $oldQty = $this->getQty();
        $qty = $this->_prepareQty($qty);

        /**
         * We can't modify quantity of existing items which have parent
         * This qty declared just once during add process and is not editable
         */
        if (!$this->getParentItem() || !$this->getId()) {
            $this->setQtyToAdd($qty);
            $this->setQty($oldQty+$qty);
        }
        return $this;
    }

    /**
     * Declare quote item quantity
     *
     * @param float $qty
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function setQty($qty)
    {
        $qty    = $this->_prepareQty($qty);
        $oldQty = $this->_getData('qty');
        $this->setData('qty', $qty);

        $this->_eventManager->dispatch('sales_quote_item_qty_set_after', array('item' => $this));

        if ($this->getQuote() && $this->getQuote()->getIgnoreOldQty()) {
            return $this;
        }
        if ($this->getUseOldQty()) {
            $this->setData('qty', $oldQty);
        }

        return $this;
    }

    /**
     * Retrieve option product with Qty
     *
     * Return array
     * 'qty'        => the qty
     * 'product'    => the product model
     *
     * @return array
     */
    public function getQtyOptions()
    {
        $qtyOptions = $this->getData('qty_options');
        if (is_null($qtyOptions)) {
            $productIds = array();
            $qtyOptions = array();
            foreach ($this->getOptions() as $option) {
                /** @var $option \Magento\Sales\Model\Quote\Item\Option */
                if (is_object($option->getProduct())
                    && $option->getProduct()->getId() != $this->getProduct()->getId()
                ) {
                    $productIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                }
            }

            foreach ($productIds as $productId) {
                $option = $this->getOptionByCode('product_qty_' . $productId);
                if ($option) {
                    $qtyOptions[$productId] = $option;
                }
            }

            $this->setData('qty_options', $qtyOptions);
        }

        return $qtyOptions;
    }

    /**
     * Set option product with Qty
     *
     * @param  $qtyOptions
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function setQtyOptions($qtyOptions)
    {
        return $this->setData('qty_options', $qtyOptions);
    }

    /**
     * Setup product for quote item
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Sales\Model\Quote\Item
     */
    public function setProduct($product)
    {
        if ($this->getQuote()) {
            $product->setStoreId($this->getQuote()->getStoreId());
            $product->setCustomerGroupId($this->getQuote()->getCustomerGroupId());
        }
        $this->setData('product', $product)
            ->setProductId($product->getId())
            ->setProductType($product->getTypeId())
            ->setSku($this->getProduct()->getSku())
            ->setName($product->getName())
            ->setWeight($this->getProduct()->getWeight())
            ->setTaxClassId($product->getTaxClassId())
            ->setBaseCost($product->getCost())
            ->setIsRecurring($product->getIsRecurring());

        if ($product->getStockItem()) {
            $this->setIsQtyDecimal($product->getStockItem()->getIsQtyDecimal());
        }

        $this->_eventManager->dispatch('sales_quote_item_set_product', array(
            'product' => $product,
            'quote_item'=>$this
        ));

        return $this;
    }

    /**
     * Check product representation in item
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  bool
     */
    public function representProduct($product)
    {
        $itemProduct = $this->getProduct();
        if (!$product || $itemProduct->getId() != $product->getId()) {
            return false;
        }

        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }

        // Check options
        $itemOptions    = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        return true;
    }

    /**
     * Check if two options array are identical
     * First options array is prerogative
     * Second options array checked against first one
     *
     * @param array $options1
     * @param array $options2
     * @return bool
     */
    public function compareOptions($options1, $options2)
    {
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (in_array($code, $this->_notRepresentOptions)) {
                continue;
            }
            if (!isset($options2[$code])
                || ($options2[$code]->getValue() === null)
                || $options2[$code]->getValue() != $option->getValue()
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Compare item
     *
     * @param   \Magento\Sales\Model\Quote\Item $item
     * @return  bool
     */
    public function compare($item)
    {
        if ($this->getProductId() != $item->getProductId()) {
            return false;
        }
        foreach ($this->getOptions() as $option) {
            if (in_array($option->getCode(), $this->_notRepresentOptions)) {
                continue;
            }
            $itemOption = $item->getOptionByCode($option->getCode());
            if ($itemOption) {
                $itemOptionValue = $itemOption->getValue();
                $optionValue     = $option->getValue();

                // dispose of some options params, that can cramp comparing of arrays
                if (is_string($itemOptionValue) && is_string($optionValue)) {
                    $_itemOptionValue = @unserialize($itemOptionValue);
                    $_optionValue     = @unserialize($optionValue);
                    if (is_array($_itemOptionValue) && is_array($_optionValue)) {
                        $itemOptionValue = $_itemOptionValue;
                        $optionValue     = $_optionValue;
                        // looks like it does not break bundle selection qty
                        unset($itemOptionValue['qty'], $itemOptionValue['uenc']);
                        unset($optionValue['qty'], $optionValue['uenc']);
                    }
                }

                if ($itemOptionValue != $optionValue) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Get item product type
     *
     * @return string
     */
    public function getProductType()
    {
        $option = $this->getOptionByCode('product_type');
        if ($option) {
            return $option->getValue();
        }
        $product = $this->getProduct();
        if ($product) {
            return $product->getTypeId();
        }
        return $this->_getData('product_type');
    }

    /**
     * Return real product type of item
     *
     * @return unknown
     */
    public function getRealProductType()
    {
        return $this->_getData('product_type');
    }

    /**
     * Convert Quote Item to array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $data = parent::toArray($arrAttributes);

        $product = $this->getProduct();
        if ($product) {
            $data['product'] = $product->toArray();
        }
        return $data;
    }

    /**
     * Initialize quote item options
     *
     * @param   array $options
     * @return  \Magento\Sales\Model\Quote\Item
     */
    public function setOptions($options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return $this;
    }

    /**
     * Get all item options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get all item options as array with codes in array key
     *
     * @return array
     */
    public function getOptionsByCode()
    {
        return $this->_optionsByCode;
    }

    /**
     * Add option to item
     *
     * @param   \Magento\Sales\Model\Quote\Item\Option|\Magento\Object $option
     * @return  \Magento\Sales\Model\Quote\Item
     * @throws \Magento\Core\Exception
     */
    public function addOption($option)
    {
        if (is_array($option)) {
            $option = $this->_itemOptionFactory->create()->setData($option)
                ->setItem($this);
        } elseif (($option instanceof \Magento\Object) && !($option instanceof \Magento\Sales\Model\Quote\Item\Option)) {
            $option = $this->_itemOptionFactory->create()->setData($option->getData())
               ->setProduct($option->getProduct())
               ->setItem($this);
        } elseif ($option instanceof \Magento\Sales\Model\Quote\Item\Option) {
            $option->setItem($this);
        } else {
            throw new \Magento\Core\Exception(__('We found an invalid item option format.'));
        }

        $exOption = $this->getOptionByCode($option->getCode());
        if ($exOption) {
            $exOption->addData($option->getData());
        } else {
            $this->_addOptionCode($option);
            $this->_options[] = $option;
        }
        return $this;
    }

    /**
     * Can specify specific actions for ability to change given quote options values
     * Example: cataloginventory decimal qty validation may change qty to int,
     * so need to change quote item qty option value.
     *
     * @param \Magento\Object $option
     * @param int|float|null $value
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function updateQtyOption(\Magento\Object $option, $value)
    {
        $optionProduct  = $option->getProduct();
        $options        = $this->getQtyOptions();

        if (isset($options[$optionProduct->getId()])) {
            $options[$optionProduct->getId()]->setValue($value);
        }

        $this->getProduct()->getTypeInstance()
            ->updateQtyOption($this->getOptions(), $option, $value, $this->getProduct());

        return $this;
    }

    /**
     *Remove option from item options
     *
     * @param string $code
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function removeOption($code)
    {
        $option = $this->getOptionByCode($code);
        if ($option) {
            $option->isDeleted(true);
        }
        return $this;
    }

    /**
     * Register option code
     *
     * @param   \Magento\Sales\Model\Quote\Item\Option $option
     * @return  \Magento\Sales\Model\Quote\Item
     * @throws \Magento\Core\Exception
     */
    protected function _addOptionCode($option)
    {
        if (!isset($this->_optionsByCode[$option->getCode()])) {
            $this->_optionsByCode[$option->getCode()] = $option;
        } else {
            throw new \Magento\Core\Exception(__('An item option with code %1 already exists.', $option->getCode()));
        }
        return $this;
    }

    /**
     * Get item option by code
     *
     * @param   string $code
     * @return  \Magento\Sales\Model\Quote\Item\Option || null
     */
    public function getOptionByCode($code)
    {
        if (isset($this->_optionsByCode[$code]) && !$this->_optionsByCode[$code]->isDeleted()) {
            return $this->_optionsByCode[$code];
        }
        return null;
    }

    /**
     * Checks that item model has data changes.
     * Call save item options if model isn't need to save in DB
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Save item options
     *
     * @return \Magento\Sales\Model\Quote\Item
     */
    protected function _saveItemOptions()
    {
        foreach ($this->_options as $index => $option) {
            if ($option->isDeleted()) {
                $option->delete();
                unset($this->_options[$index]);
                unset($this->_optionsByCode[$option->getCode()]);
            } else {
                $option->save();
            }
        }

        $this->_flagOptionsSaved = true; // Report to watchers that options were saved

        return $this;
    }

    /**
     * Save model plus its options
     * Ensures saving options in case when resource model was not changed
     */
    public function save()
    {
        $hasDataChanges = $this->hasDataChanges();
        $this->_flagOptionsSaved = false;

        parent::save();

        if ($hasDataChanges && !$this->_flagOptionsSaved) {
            $this->_saveItemOptions();
        }
    }

    /**
     * Save item options after item saved
     *
     * @return \Magento\Sales\Model\Quote\Item
     */
    protected function _afterSave()
    {
        $this->_saveItemOptions();
        return parent::_afterSave();
    }

    /**
     * Clone quote item
     *
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function __clone()
    {
        parent::__clone();
        $options = $this->getOptions();
        $this->_quote           = null;
        $this->_options         = array();
        $this->_optionsByCode   = array();
        foreach ($options as $option) {
            $this->addOption(clone $option);
        }
        return $this;
    }

    /**
     * Returns formatted buy request - object, holding request received from
     * product view page with keys and options for configured product
     *
     * @return \Magento\Object
     */
    public function getBuyRequest()
    {
        $option = $this->getOptionByCode('info_buyRequest');
        $buyRequest = new \Magento\Object($option ? unserialize($option->getValue()) : null);

        // Overwrite standard buy request qty, because item qty could have changed since adding to quote
        $buyRequest->setOriginalQty($buyRequest->getQty())
            ->setQty($this->getQty() * 1);

        return $buyRequest;
    }

    /**
     * Sets flag, whether this quote item has some error associated with it.
     *
     * @param bool $flag
     * @return \Magento\Sales\Model\Quote\Item
     */
    protected function _setHasError($flag)
    {
        return $this->setData('has_error', $flag);
    }

    /**
     * Sets flag, whether this quote item has some error associated with it.
     * When TRUE - also adds 'unknown' error information to list of quote item errors.
     * When FALSE - clears whole list of quote item errors.
     * It's recommended to use addErrorInfo() instead - to be able to remove error statuses later.
     *
     * @param bool $flag
     * @return \Magento\Sales\Model\Quote\Item
     * @see addErrorInfo()
     */
    public function setHasError($flag)
    {
        if ($flag) {
            $this->addErrorInfo();
        } else {
            $this->_clearErrorInfo();
        }
        return $this;
    }

    /**
     * Clears list of errors, associated with this quote item.
     * Also automatically removes error-flag from oneself.
     *
     * @return \Magento\Sales\Model\Quote\Item
     */
    protected function _clearErrorInfo()
    {
        $this->_errorInfos->clear();
        $this->_setHasError(false);
        return $this;
    }

    /**
     * Adds error information to the quote item.
     * Automatically sets error flag.
     *
     * @param string|null $origin Usually a name of module, that embeds error
     * @param int|null $code Error code, unique for origin, that sets it
     * @param string|null $message Error message
     * @param \Magento\Object|null $additionalData Any additional data, that caller would like to store
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function addErrorInfo($origin = null, $code = null, $message = null, $additionalData = null)
    {
        $this->_errorInfos->addItem($origin, $code, $message, $additionalData);
        if ($message !== null) {
            $this->setMessage($message);
        }
        $this->_setHasError(true);

        return $this;
    }

    /**
     * Retrieves all error infos, associated with this item
     *
     * @return array
     */
    public function getErrorInfos()
    {
        return $this->_errorInfos->getItems();
    }

    /**
     * Removes error infos, that have parameters equal to passed in $params.
     * $params can have following keys (if not set - then any item is good for this key):
     *   'origin', 'code', 'message'
     *
     * @param array $params
     * @return \Magento\Sales\Model\Quote\Item
     */
    public function removeErrorInfosByParams($params)
    {
        $removedItems = $this->_errorInfos->removeItemsByParams($params);
        foreach ($removedItems as $item) {
            if ($item['message'] !== null) {
                $this->removeMessageByText($item['message']);
            }
        }

        if (!$this->_errorInfos->getItems()) {
            $this->_setHasError(false);
        }

        return $this;
    }
}
