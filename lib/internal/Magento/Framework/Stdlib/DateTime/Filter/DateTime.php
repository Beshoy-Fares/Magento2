<?php
/**
 * Date/Time filter. Converts datetime from localized to internal format.
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Stdlib\DateTime\Filter;

class DateTime extends Date
{
    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * 
     * @deprecated 
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        parent::__construct($localeDate);
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(
            [
                'date_format' => $this->_localeDate->getDateTimeFormat(
                    \IntlDateFormatter::SHORT
                ),
            ]
        );
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT]
        );
    }

    /**
     * Convert date from localized to internal format
     *
     * @param string $value
     * @return string
     */

    public function filter($value)
    {
        $value = new \DateTime($value, new \DateTimeZone('UTC'));
        return $value->format('Y-m-d H:i:s');
    }
}
