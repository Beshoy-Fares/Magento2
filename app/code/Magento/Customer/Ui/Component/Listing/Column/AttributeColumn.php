<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;

class AttributeColumn extends Column
{
    /** @var AttributeRepository */
    protected $attributeRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AttributeRepository $attributeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AttributeRepository $attributeRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array &$dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return;
        }

        $attributeCode = isset($this->getData('config')['origin'])
            ? $this->getData('config')['origin']
            : $this->getName();
        $metaData = $this->attributeRepository->getMetadataByCode($attributeCode);
        if ($metaData && count($metaData->getOptions())) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item[$this->getName()])) {
                    continue;
                }
                foreach ($metaData->getOptions() as $option) {
                    if ($option->getValue() == $item[$this->getName()]) {
                        $item[$this->getName()] = $option->getLabel();
                        break;
                    }
                }
            }
        }
    }
}
