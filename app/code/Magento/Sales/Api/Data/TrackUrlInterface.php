<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Api\Data;

/**
 * Shipment Track Url Creation interface.
 *
 * @api
 * @since 100.2.1
 */
interface TrackUrlInterface
{
    /**
     * Sets the track URL for the shipment package.
     *
     * @param string $trackUrl
     * @return $this
     */
    public function setTrackUrl($trackUrl);

    /**
     * Gets the track URL for the shipment package.
     *
     * @return string Track URL.
     */
    public function getTrackUrl();
}
