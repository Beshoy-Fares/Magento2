<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\InstantPurchase\Model\ShippingMethodChoose;

/**
 * Container to register available deferred shipping method choosers.
 * Use deferred shipping method code as a key for a deferred chooser.
 *
 * @api
 */
class DeferredShippingMethodChooserPool
{
    private $choosers;

<<<<<<< HEAD
    /**
     * @param DeferredShippingMethodChooserInterface[] $choosers
     */
=======
>>>>>>> upstream/2.2-develop
    public function __construct(array $choosers)
    {
        foreach ($choosers as $chooser) {
            if (!$chooser instanceof DeferredShippingMethodChooserInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid configuration. Chooser should be instance of %s.',
                    DeferredShippingMethodChooserInterface::class
                ));
            }
        }
        $this->choosers = $choosers;
    }

<<<<<<< HEAD
    /**
     * @param string $type
     * @return DeferredShippingMethodChooserInterface
     */
=======
>>>>>>> upstream/2.2-develop
    public function get($type) : DeferredShippingMethodChooserInterface
    {
        if (!isset($this->choosers[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Deferred shipping method chooser is not registered.',
                $type
            ));
        }

        return $this->choosers[$type];
    }
}
