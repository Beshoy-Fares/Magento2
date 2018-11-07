<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Setup;

use Magento\Eav\Model\Config;
use Magento\Framework\App\State;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory as AddressCollectionFactory;

/**
 * Data upgrade script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @var AddressCollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var State
     */
    private $state;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     * @param Config $eavConfig
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     * @param AddressCollectionFactory $addressCollFactory
     * @param OrderFactory $orderFactory
     * @param QuoteFactory $quoteFactory
     * @param State $state
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        Config $eavConfig,
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        AddressCollectionFactory $addressCollFactory,
        OrderFactory $orderFactory,
        QuoteFactory $quoteFactory,
        State $state
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->addressCollectionFactory = $addressCollFactory;
        $this->orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $salesSetup->updateEntityTypes();
        }
        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->convertSerializedDataToJson($context->getVersion(), $salesSetup);
        }
        if (version_compare($context->getVersion(), '2.0.8', '<')) {
            $this->state->emulateAreaCode(
                \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                [$this, 'fillQuoteAddressIdInSalesOrderAddress'],
                [$setup]
            );
        }
        if (version_compare($context->getVersion(), '2.0.9', '<')) {
            //Correct wrong source model for "invoice" entity type, introduced by mistake in 2.0.1 upgrade.
            $salesSetup->updateEntityType(
                'invoice',
                'entity_model',
                \Magento\Sales\Model\ResourceModel\Order\Invoice::class
            );
        }
        $this->eavConfig->clear();
    }

    /**
     * Convert data from serialized to JSON encoded
     *
     * @param string $setupVersion
     * @param SalesSetup $salesSetup
     * @return void
     */
    private function convertSerializedDataToJson($setupVersion, SalesSetup $salesSetup)
    {
        $fieldsToUpdate = [
            new FieldToConvert(
                SerializedToJson::class,
                $salesSetup->getTable('sales_invoice_item'),
                'entity_id',
                'tax_ratio'
            ),
            new FieldToConvert(
                SerializedToJson::class,
                $salesSetup->getTable('sales_creditmemo_item'),
                'entity_id',
                'tax_ratio'
            ),
        ];
        if (version_compare($setupVersion, '2.0.5', '<')) {
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedDataConverter::class,
                $salesSetup->getTable('sales_order_item'),
                'item_id',
                'product_options'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $salesSetup->getTable('sales_shipment'),
                'entity_id',
                'packages'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SalesOrderPaymentDataConverter::class,
                $salesSetup->getTable('sales_order_payment'),
                'entity_id',
                'additional_information'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $salesSetup->getTable('sales_payment_transaction'),
                'transaction_id',
                'additional_information'
            );
        }
        $this->aggregatedFieldConverter->convert($fieldsToUpdate, $salesSetup->getConnection());
    }

    /**
     * Fill quote_address_id in table sales_order_address if it is empty.
     *
     * @param ModuleDataSetupInterface $setup
     */
    public function fillQuoteAddressIdInSalesOrderAddress(ModuleDataSetupInterface $setup)
    {
        $this->fillQuoteAddressIdInSalesOrderAddressByType($setup,Address::TYPE_SHIPPING);
        $this->fillQuoteAddressIdInSalesOrderAddressByType($setup,Address::TYPE_BILLING);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param string $address_type
     */
    public function fillQuoteAddressIdInSalesOrderAddressByType(ModuleDataSetupInterface $setup, $address_type)
    {
        $salesConnection = $setup->getConnection('sales');
        $salesOrderTable = $setup->getTable('sales_order', 'sales');
        $salesOrderAddressTable = $setup->getTable('sales_order_address', 'sales');

        $query = $salesConnection
            ->select()
            ->from($salesOrderAddressTable, ['entity_id', 'address_type'])
            ->joinInner(
                ['sales_order' => $salesOrderTable],
                'sales_order_address.parent_id = sales_order.entity_id',
                ['quote_id' => 'sales_order.quote_id']
            )
            ->where('sales_order_address.quote_address_id IS NULL')
            ->where('sales_order_address.address_type = ?', $address_type)
            ->order('sales_order_address.entity_id')
        ;

        $batchSize = 5000;
        $result = $salesConnection->query($query);
        $count = $result->rowCount();
        $batches = ceil($count / $batchSize);

        for ($batch = $batches; $batch > 0; $batch--) {
            $query->limitPage($batch, $batchSize);
            $result = $salesConnection->fetchAssoc($query);
            $this->fillQuoteAddressIdInSalesOrderAddressProcessBatch($setup, $result, $address_type);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $orderAddresses
     * @param string $address_type
     */
    public function fillQuoteAddressIdInSalesOrderAddressProcessBatch(ModuleDataSetupInterface $setup, array $orderAddresses, $address_type)
    {
        $salesConnection = $setup->getConnection('sales');
        $quoteConnection = $setup->getConnection('checkout');

        $query = $quoteConnection
            ->select()
            ->from($setup->getTable('quote_address', 'checkout'), ['quote_id', 'address_id'])
            ->joinInner(
                ['quote' => $setup->getTable('quote', 'checkout')],
                'quote_address.quote_id = quote.entity_id',
                []
            )
            ->where('quote.entity_id in (?)', array_keys($orderAddresses))
            ->where('address_type = ?', $address_type)
        ;

        $quoteAddresses = $quoteConnection->fetchAssoc($query);

        $sql = "update `{$setup->getTable('sales_order_address', 'sales')}` set `quote_address_id` = :quoteAddressId where `entity_id` = :orderAddressId";
        $stmt = $salesConnection->prepare($sql);

        foreach ($orderAddresses as $orderAddress) {
            $stmt->execute([
                'quoteAddressId' => $quoteAddresses[$orderAddress['quote_id']]['address_id'] ?? null,
                'orderAddressId' => $orderAddress['entity_id'],
            ]);
        }
    }
}
