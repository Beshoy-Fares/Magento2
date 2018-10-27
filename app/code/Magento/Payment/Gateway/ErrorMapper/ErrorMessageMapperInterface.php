<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> upstream/2.2-develop
namespace Magento\Payment\Gateway\ErrorMapper;

use Magento\Framework\Phrase;

/**
 * Interface to provide customization for payment validation errors.
<<<<<<< HEAD
 *
 * @api
=======
>>>>>>> upstream/2.2-develop
 */
interface ErrorMessageMapperInterface
{
    /**
     * Returns customized error message by provided code.
     * If message not found `null` will be returned.
     *
     * @param string $code
     * @return Phrase|null
     */
    public function getMessage(string $code);
}
