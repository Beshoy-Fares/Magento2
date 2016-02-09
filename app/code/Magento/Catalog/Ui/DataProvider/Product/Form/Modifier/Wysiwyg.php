<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\AttributeConstantsInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;

/**
 * Class Wysiwyg
 */
class Wysiwyg extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManger;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManger = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $this->addMetaProperties($meta, [
            AttributeConstantsInterface::CODE_DESCRIPTION,
            AttributeConstantsInterface::CODE_SHORT_DESCRIPTION,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Add additional meta properties
     *
     * @param array $meta
     * @param array $fields
     * @return array
     */
    protected function addMetaProperties(array $meta, array $fields)
    {
        foreach ($fields as $attributeCode) {
            if ($this->getGroupCodeByField($meta, $attributeCode)) {
                $attributePath = $this->getElementArrayPath($meta, $attributeCode);

                $meta = $this->arrayManger->merge($attributePath, $meta, [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'wysiwyg' => true,
                                'formElement' => WysiwygElement::NAME,
                            ],
                        ],
                    ],
                ]);
            }
        }

        return $meta;
    }
}
