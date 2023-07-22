<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Widget Instance edit container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Widget\Model\Widget\Instance;

/**
 * @api
 * @since 100.0.2
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'instance_id';
        $this->_blockGroup = 'Magento_Widget';
        $this->_controller = 'adminhtml_widget_instance';
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return Instance
     */
    public function getWidgetInstance()
    {
        return $this->_coreRegistry->registry('current_widget_instance');
    }

    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @return $this
     */
    protected function _preparelayout()
    {
        if ($this->getWidgetInstance()->isCompleteToCreate()) {
            $this->buttonList->add(
                'save_and_edit_button',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                100
            );
        } else {
            $this->removeButton('save');
        }
        return parent::_prepareLayout();
    }

    /**
     * Return translated header text depending on creating/editing action
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        if ($this->getWidgetInstance()->getId()) {
            return __('Widget "%1"', $this->escapeHtml($this->getWidgetInstance()->getTitle()));
        } else {
            return __('New Widget Instance');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('adminhtml/*/validate', ['_current' => true]);
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('adminhtml/*/save', ['_current' => true, 'back' => null]);
    }
}
