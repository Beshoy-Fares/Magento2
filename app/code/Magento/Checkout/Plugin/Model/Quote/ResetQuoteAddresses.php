<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Checkout\Plugin\Model\Quote;

use Magento\Quote\Model\Quote;

/**
 * Clear quote addresses after all items were removed.
 */
class ResetQuoteAddresses
{
    /**
<<<<<<< HEAD
     * Clears the quote addresses when all the items are removed from the cart
     *
     * @param Quote $quote
     * @param Quote $result
     * @param mixed $itemId
=======
     * @param Quote $quote
     * @param Quote $result
     * @param mixed $itemId
     *
>>>>>>> upstream/2.2-develop
     * @return Quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRemoveItem(Quote $quote, Quote $result, $itemId): Quote
    {
        if (empty($result->getAllVisibleItems())) {
            foreach ($result->getAllAddresses() as $address) {
                $result->removeAddress($address->getId());
            }
<<<<<<< HEAD
            $extensionAttributes = $result->getExtensionAttributes();
            if (!$result->isVirtual() && $extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
=======
>>>>>>> upstream/2.2-develop
        }

        return $result;
    }
}
