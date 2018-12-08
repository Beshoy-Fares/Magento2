<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
=======
declare(strict_types=1);

>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
namespace Magento\Payment\Gateway\ErrorMapper;

use Magento\Framework\Config\DataInterface;

/**
 * This class can be used for payment integrations which can validate different type of
 * error messages per one request.
 * For example, during authorization payment operation the payment integration can validate error messages
 * related to credit card details and customer address data.
 * In that case, this implementation can be extended via di.xml and configured with appropriate mappers.
<<<<<<< HEAD
=======
 *
 * @api
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
 */
class ErrorMessageMapper implements ErrorMessageMapperInterface
{
    /**
     * @var DataInterface
     */
    private $messageMapping;

    /**
     * @param DataInterface $messageMapping
     */
    public function __construct(DataInterface $messageMapping)
    {
        $this->messageMapping = $messageMapping;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(string $code)
    {
        $message = $this->messageMapping->get($code);
        return $message ? __($message) : null;
    }
}
