<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model\Entity\Attribute;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Eav\Model\ResourceModel\GetAttributeOptionId;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use \Magento\Framework\App\ObjectManager;

/**
 * Eav Option Management
 */
class OptionManagement implements AttributeOptionManagementInterface
{
    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var Attribute
     */
    protected $resourceModel;

    /**
     * @var GetAttributeOptionId
     */
    private $getAttributeOptionId;

    /**
     * @param AttributeRepository $attributeRepository
     * @param Attribute $resourceModel
     * @param GetAttributeOptionId $getAttributeOptionId
     * @codeCoverageIgnore
     */
    public function __construct(
        AttributeRepository $attributeRepository = null,
        Attribute $resourceModel,
        GetAttributeOptionId $getAttributeOptionId = null
    ) {
        $this->attributeRepository = $attributeRepository ?: ObjectManager::getInstance()->get(
            AttributeRepository::class
        );
        $this->resourceModel = $resourceModel;
        $this->getAttributeOptionId = $getAttributeOptionId ?: ObjectManager::getInstance()->get(
            GetAttributeOptionId::class
        );
    }

    /**
     * @inheritdoc
     */
    public function add($entityType, $attributeCode, $option)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('The attribute code is empty. Enter the code and try again.'));
        }

        $attribute = $this->attributeRepository->get($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(__('The "%1" attribute doesn\'t work with options.', $attributeCode));
        }

        $optionLabel = $option->getLabel();
        $optionValue = $this->getOptionValue($option);
        $options = [];
        $options['value'][$optionValue][0] = $optionLabel;
        $options['order'][$optionValue] = $option->getSortOrder();
        $attributeId = $attribute->getAttributeId();

        $options = $this->processStoreLabels($option, $options, $optionValue);

        if ($option->getIsDefault()) {
            $attribute->setDefault([$optionValue]);
        }

        $attribute->setOption($options);
        try {
            $this->resourceModel->save($attribute);
            if ($optionLabel && $attribute->getAttributeCode()) {
                $this->setOptionValue($option, $attribute, $optionLabel);
            }
        } catch (\Exception $e) {
            throw new StateException(__('The "%1" attribute can\'t be saved.', $attributeCode));
        }

        $optionValue = is_array($option->getStoreLabels()) ? $optionValue : $optionLabel;

        return $this->getAttributeOptionId->execute($attributeId, $optionValue);
    }

    /**
     * @inheritdoc
     */
    public function delete($entityType, $attributeCode, $optionId)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('The attribute code is empty. Enter the code and try again.'));
        }

        $attribute = $this->attributeRepository->get($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(__('The "%1" attribute has no option.', $attributeCode));
        }
        $this->validateOption($attribute, $optionId);

        $removalMarker = [
            'option' => [
                'value' => [$optionId => []],
                'delete' => [$optionId => '1'],
            ],
        ];
        $attribute->addData($removalMarker);
        try {
            $this->resourceModel->save($attribute);
        } catch (\Exception $e) {
            throw new StateException(__('The "%1" attribute can\'t be saved.', $attributeCode));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getItems($entityType, $attributeCode)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('The attribute code is empty. Enter the code and try again.'));
        }
        $attribute = $this->attributeRepository->get($entityType, $attributeCode);

        try {
            $options = $attribute->getOptions();
        } catch (\Exception $e) {
            throw new StateException(__('The options for "%1" attribute can\'t be loaded.', $attributeCode));
        }

        return $options;
    }

    /**
     * Validate option
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @param int $optionId
     * @throws NoSuchEntityException
     * @return void
     */
    protected function validateOption($attribute, $optionId)
    {
        if ($attribute->getSource()->getOptionText($optionId) === false) {
            throw new NoSuchEntityException(
                __(
                    'The "%1" attribute doesn\'t include an option with "%2" ID.',
                    $attribute->getAttributeCode(),
                    $optionId
                )
            );
        }
    }

    /**
     * Returns option value
     *
     * @param AttributeOptionInterface $option
     * @return string
     */
    private function getOptionValue(AttributeOptionInterface $option) : string
    {
        return 'value_' . ($option->getValue() ?: 'new_option');
    }

    /**
     * Set option value
     *
     * @param AttributeOptionInterface $option
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @param string $optionLabel
     * @return void
     */
    private function setOptionValue(
        AttributeOptionInterface $option,
        \Magento\Eav\Api\Data\AttributeInterface $attribute,
        string $optionLabel
    ) {
        $optionId = $attribute->getSource()->getOptionId($optionLabel);
        if ($optionId) {
            $option->setValue($attribute->getSource()->getOptionId($optionId));
        } elseif (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                if ($optionId = $attribute->getSource()->getOptionId($label->getLabel())) {
                    $option->setValue($attribute->getSource()->getOptionId($optionId));
                    break;
                }
            }
        }
    }

    /**
     * Process option store labels.
     *
     * @param AttributeOptionInterface $option
     * @param array $options
     * @param string $optionValue
     * @return array
     */
    private function processStoreLabels(AttributeOptionInterface $option, array $options, string $optionValue): array
    {
        if (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                $options['value'][$optionValue][$label->getStoreId()] = $label->getLabel();
            }
        }

        return $options;
    }
}
