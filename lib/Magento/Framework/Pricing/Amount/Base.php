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
 * @package     Magento_Catalog
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Framework\Pricing\Amount;

use Magento\Framework\Pricing\Adjustment\AdjustmentInterface;

/**
 * Amount base model
 */
class Base implements AmountInterface
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var float
     */
    protected $baseAmount;

    /**
     * @var float
     */
    protected $totalAdjustmentAmount;

    /**
     * @var float[]
     */
    protected $adjustmentAmounts = [];

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments = [];

    /**
     * @param float $amount
     * @param array $adjustmentAmounts
     */
    public function __construct(
        $amount,
        array $adjustmentAmounts = []
    ) {
        $this->amount = $amount;
        $this->adjustmentAmounts = $adjustmentAmounts;
    }

    /**
     * Return full amount value
     *
     * @param null|string|array $exclude
     * @return float
     */
    public function getValue($exclude = null)
    {
        if ($exclude === null) {
            return $this->amount;
        } else {
            if (!is_array($exclude)) {
                $exclude = [(string)$exclude];
            }
            $amount = $this->amount;
            foreach ($exclude as $code) {
                if ($this->hasAdjustment($code)) {
                    $amount -= $this->adjustmentAmounts[$code];
                }
            }
            return $amount;
        }
    }

    /**
     * Return full amount value in string format
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Return base amount part value
     *
     * @return float|null
     */
    public function getBaseAmount()
    {
        if ($this->baseAmount === null) {
            $this->calculateAmounts();
        }
        return $this->baseAmount;
    }

    /**
     * Return adjustment amount part value by adjustment code
     *
     * @param string $adjustmentCode
     * @return bool|float
     */
    public function getAdjustmentAmount($adjustmentCode)
    {
        return isset($this->adjustmentAmounts[$adjustmentCode])
            ? $this->adjustmentAmounts[$adjustmentCode]
            : false;
    }

    /**
     * Return sum amount of all applied adjustments
     *
     * @return float|null
     */
    public function getTotalAdjustmentAmount()
    {
        if ($this->totalAdjustmentAmount === null) {
            $this->calculateAmounts();
        }
        return $this->totalAdjustmentAmount;
    }

    /**
     * Return all applied adjustments as array
     *
     * @return float[]
     */
    public function getAdjustmentAmounts()
    {
        return $this->adjustmentAmounts;
    }

    /**
     * Check if adjustment is contained in amount object
     *
     * @param string $adjustmentCode
     * @return bool
     */
    public function hasAdjustment($adjustmentCode)
    {
        return array_key_exists($adjustmentCode, $this->adjustmentAmounts);
    }

    /**
     * Calculate base amount
     *
     * @return void
     */
    protected function calculateAmounts()
    {
        $this->baseAmount = $this->amount;
        $this->totalAdjustmentAmount = 0.;
        if ($this->adjustmentAmounts) {
            foreach ($this->adjustmentAmounts as $amount) {
                $this->baseAmount -= $amount;
                $this->totalAdjustmentAmount += $amount;
            }
        }
    }
}
