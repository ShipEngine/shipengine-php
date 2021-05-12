<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * The location of a given tracking event.
 *
 * @property ?string $cityLocality
 * @property ?string $stateProvince
 * @property ?string $postalCode
 * @property ?string $countryCode
 * @property ?float $latitude
 * @property ?float $longitude
 */
final class Location implements \JsonSerializable
{
    /**
     * The tracking event's city/locality.
     *
     * @var string|null
     */
    public ?string $cityLocality;

    /**
     * The tracking event's state/province.
     *
     * @var string|null
     */
    public ?string $stateProvince;

    /**
     * The tracking event's postal code.
     *
     * @var string|null
     */
    public ?string $postalCode;

    /**
     * The tracking event's countryCode.
     *
     * @var string|null
     */
    public ?string $countryCode;

    /**
     * The tracking event's latitude.
     *
     * @var float|null
     */
    public ?float $latitude;

    /**
     * The tracking event's longitude.
     *
     * @var float|null
     */
    public ?float $longitude;

    /**
     * Location constructor. The location of where a given tracking event occurred.
     *
     * @param array $location_data
     */
    public function __construct(array $location_data)
    {
        $this->cityLocality = null ?? $location_data['cityLocality'];
        $this->stateProvince = null ?? $location_data['stateProvince'];
        $this->postalCode = null ?? $location_data['postalCode'];
        $this->countryCode = null ?? $location_data['countryCode'];
        $this->latitude = null ?? $location_data['coordinates']['latitude'];
        $this->longitude = null ?? $location_data['coordinates']['longitude'];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'cityLocality' => $this->cityLocality,
            'stateProvince' => $this->stateProvince,
            'postalCode' => $this->postalCode,
            'countryCode' => $this->countryCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
