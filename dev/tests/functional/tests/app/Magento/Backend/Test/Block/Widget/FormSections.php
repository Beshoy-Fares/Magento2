<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Test\Block\Widget;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Block\BlockFactory;
use Magento\Mtf\Block\Mapper;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Is used to represent a new unified form with collapsible sections on the page.
 */
class FormSections extends Form
{
    /**
     * Sections list.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * Page header selector.
     *
     * @var string
     */
    protected $header = 'header';

    /**
     * @constructor
     * @param SimpleElement $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param BrowserInterface $browser
     * @param array $config
     */
    public function __construct(
        SimpleElement $element,
        BlockFactory $blockFactory,
        Mapper $mapper,
        BrowserInterface $browser,
        array $config = []
    ) {
        parent::__construct($element, $blockFactory, $mapper, $browser, $config);
    }

    /**
     * Initialize block.
     */
    protected function init()
    {
        $this->sections = $this->getFormMapping();
    }

    /**
     * Get Section class.
     *
     * @param string $sectionName
     * @return Section
     * @throws \Exception
     */
    public function getSection($sectionName)
    {
        $sectionClass = $this->sections[$sectionName]['class'];
        /** @var Section $section */
        $section = $this->blockFactory->create($sectionClass, ['element' => $this->_rootElement]);
        if (!$section instanceof Section) {
            throw new \Exception('Wrong Section Class.');
        }
        $section->setWrapper(isset($this->sections[$sectionName]['wrapper']) ? $this->sections[$sectionName]['wrapper'] : '');
        $section->setMapping(isset($this->sections[$sectionName]['fields']) ? (array)$this->sections[$sectionName]['fields'] : []);

        return $section;
    }

    /**
     * Get data of the sections.
     *
     * @param FixtureInterface|null $fixture
     * @param SimpleElement|null $element
     * @return array
     */
    public function getData(FixtureInterface $fixture = null, SimpleElement $element = null)
    {
        $data = [];

        if (null === $fixture) {
            foreach ($this->sections as $sectionName => $sectionData) {
                $sectionData = $this->getSection($sectionName)->getSectionData();
                $data = array_merge($data, $sectionData);
            }
        } else {
            $hasData = ($fixture instanceof InjectableFixture) ? $fixture->hasData() : true;
            $dataBySections = $hasData ? $this->getFieldsBySections($fixture) : [];
            foreach ($dataBySections as $sectionName => $sectionFields) {
                if (!$sectionName) {
                    continue;
                }
                $this->openSection($sectionName);
                $sectionData = $this->getSection($sectionName)->getSectionData($sectionFields);
                $data = array_merge($data, $sectionData);
            }
        }

        return $data;
    }

    /**
     * Fill form with sections.
     *
     * @param FixtureInterface $fixture
     * @param SimpleElement|null $element
     * @return FormSections
     */
    public function fill(FixtureInterface $fixture, SimpleElement $element = null)
    {
        $sections = $this->getFieldsBySections($fixture);
        return $this->fillSections($sections, $element);
    }

    /**
     * Create data array for filling sections
     * Returns data in format
     * [[section => [field => [attribute_name => attribute_value, ..], ..], ..]
     * where section name can be empty if a field is present on the form, but not assigned to any section.
     *
     * Fixture's field should have 'group = "" ' if a field is present on the form, but is not inside any section.
     * Fixture's field should not have 'group' property if it's not present on the form.
     *
     * @param InjectableFixture $fixture
     * @return array
     */
    public function getFieldsBySections(InjectableFixture $fixture)
    {
        $dataBySection = [];
        $data = $fixture->getData();
        foreach ($data as $field => $value) {
            $attributes = $fixture->getDataFieldConfig($field);
            $attributes['value'] = $value;
            if (!isset($attributes['group'])) {
                continue;
            }
            $dataBySection[$attributes['group']][$field] = $attributes;
        }
        return $dataBySection;
    }

    /**
     * Fill specified form with sections.
     * Input data in format
     * [[section => [field => [attribute_name => attribute_value, ..], ..], ..]
     * where section name can be empty if a field is not assigned to any section.
     *
     * @param array $dataBySections
     * @param SimpleElement|null $element
     * @return FormSections
     */
    protected function fillSections(array $dataBySections, SimpleElement $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($dataBySections as $sectionName => $sectionFields) {
            if($sectionName) {
                $this->openSection($sectionName);
                $this->getSection($sectionName)->fillSection($sectionFields, $context);
            }
        }
        return $this;
    }

    /**
     * Open section.
     *
     * @param string $sectionName
     * @return FormSections
     */
    public function openSection($sectionName)
    {
        $this->browser->find($this->header)->hover();
        foreach($this->sections as $name => $sectionData) {
            if ($name != $sectionName) {
                $this->getSection($name)->collapse();
                continue;
            }
            $this->getSection($name)->expand();
            return $this;
        }
        return $this;
    }
}
