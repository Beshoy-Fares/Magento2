<?php

namespace Magento\CatalogInventory\Api\Data;

use Magento\Directory\Api\Data\RegionInformationInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Editable representation of inventory interface
 *
 * This object should never be used in the frontend
 *
 * @api
 */
interface WarehouseInterface extends InventoryDataInterface, ExtensibleDataInterface
{
    /**
     * Sets unique code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Sets warehouse as virtual
     *
     * @param int $flag
     * @return $this
     */
    public function setIsVirtual($flag);

    /**
     * Returns warehouse virtual flag
     *
     * @return int
     */
    public function getIsVirtual();

    /**
     * Sets warehouse as indexed
     *
     * @param int $flag
     * @return $this
     */
    public function setIsIndexed($flag);

    /**
     * Returns warehouse indexed flag
     *
     * @return int
     */
    public function getIsIndexed();

    /**
     * Company name of the warehouse location
     *
     * @param string $company
     * @return $this
     */
    public function setCompany($company);

    /**
     * Sets multi-line street address for warehouse location
     *
     * @param string[] $street
     * @return $this
     */
    public function setStreet(array $street);

    /**
     * Returns city name for warehouse location
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Returns postcode (zipcode) for warehouse location
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Sets region as relation to RegionInformationInterface
     *
     * @param RegionInformationInterface $region
     * @return $this
     */
    public function setRegion(RegionInformationInterface $region);

    /**
     * Sets region as custom text value, by canceling model relation
     *
     * @param string $regionText
     * @return $this
     */
    public function setRegionText($regionText);

    /**
     * Sets country identifier for warehouse location
     *
     * @param int $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Sets latitude of location for warehouse location
     *
     * @param string $latitude
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * Sets longitude of location for warehouse location
     *
     * @param string $longitude
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * Sets configuration to be overridden for a warehouse
     *
     * @param WarehouseConfigurationInterface $configuration
     * @return $this
     */
    public function setConfiguration(WarehouseConfigurationInterface $configuration);

    /**
     * Returns warehouse configuration
     *
     * @return WarehouseConfigurationInterface
     */
    public function getConfiguration();
}
