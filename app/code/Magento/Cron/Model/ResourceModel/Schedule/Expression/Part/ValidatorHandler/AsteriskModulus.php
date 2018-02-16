<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cron\Model\ResourceModel\Schedule\Expression\Part\ValidatorHandler;

use Magento\Cron\Model\ResourceModel\Schedule\Expression\Part\ValidatorHandlerFactory;
use Magento\Cron\Model\ResourceModel\Schedule\Expression\PartInterface;

/**
 * Cron expression sub part validator handler class
 *
 * @api
 */
class AsteriskModulus implements ValidatorHandlerInterface
{
    /**
     * @var ValidatorHandlerFactory
     */
    private $validatorHandlerFactory;

    /**
     * AsteriskModulus constructor.
     *
     * @param ValidatorHandlerFactory $validatorHandlerFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        ValidatorHandlerFactory $validatorHandlerFactory
    ) {
        $this->validatorHandlerFactory = $validatorHandlerFactory;
    }

    /**
     * Handle cron expression sub part
     *
     * Returns
     * - If valid:
     *   - original/modified $subPartValue, to continue processing other handles
     *   - true, to stop executing next handles
     * - If not valid
     *   - false, to stop executing next handles
     *
     * @param PartInterface $part
     * @param string        $subPartValue
     *
     * @return string|bool
     */
    public function handle(PartInterface $part, $subPartValue)
    {
        return $this->validatorHandlerFactory
            ->create(ValidatorHandlerFactory::ASTERISK_VALIDATION_HANDLER)
            ->handle($part, $subPartValue);
    }
}
