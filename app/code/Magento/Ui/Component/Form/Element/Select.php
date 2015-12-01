<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Component\Form\Element;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Select
 */
class Select extends AbstractElement
{
    const NAME = 'select';

    /**
     * @var array|OptionSourceInterface|null
     */
    protected $options;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param array|OptionSourceInterface|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        $this->options = $options;
        parent::__construct($context, $components, $data);
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (isset($this->options)) {
            if (!isset($config['options'])) {
                $config['options'] = [];
            }
            if ($this->options instanceof OptionSourceInterface) {
                $options = $this->options->toOptionArray();
            } else {
                $options = array_values($this->options);
            }
            $options = $this->convertOptionsValueToString($options);
            $config['options'] = array_values(array_merge_recursive($options, $config['options']));
        }
        $this->setData('config', (array)$config);
        parent::prepare();
    }

    /**
     * Convert options value to string
     *
     * @param array $options
     * @return array
     */
    protected function convertOptionsValueToString(array $options)
    {
        array_walk($options, function (&$value) {
            if (isset($value['value']) && is_scalar($value['value'])) {
                $value['value'] = (string)$value['value'];
            }
        });
        return $options;
    }

    /**
     * Check if option value
     *
     * @param string $optionValue
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSelected($optionValue)
    {
        return $this->getValue() == $optionValue;
    }
}
