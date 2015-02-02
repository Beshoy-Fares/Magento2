<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model\Sample;

use Magento\Downloadable\Api\Data\SampleContentInterface;

/**
 * @codeCoverageIgnore
 */
class Content extends \Magento\Framework\Model\AbstractExtensibleModel implements SampleContentInterface
{
    const TITLE = 'title';
    const SORT_ORDER = 'sort_order';
    const SAMPLE_FILE = 'sample_file';
    const SAMPLE_URL = 'sample_url';
    const SAMPLE_TYPE = 'sample_type';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSampleType()
    {
        return $this->getData(self::SAMPLE_TYPE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSampleFile()
    {
        return $this->getData(self::SAMPLE_FILE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSampleUrl()
    {
        return $this->getData(self::SAMPLE_URL);
    }
}
