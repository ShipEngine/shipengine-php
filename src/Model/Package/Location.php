<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * The location of a given tracking event.
 *
 * @property ?string $city_locality
 * @property ?string $state_province
 * @property ?string $postal_code
 * @property ?string $country_code
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
    public ?string $city_locality;

    /**
     * The tracking event's state/province.
     *
     * @var string|null
     */
    public ?string $state_province;

    /**
     * The tracking event's postal code.
     *
     * @var string|null
     */
    public ?string $postal_code;

    /**
     * The tracking event's country.
     *
     * @var string|null
     */
    public ?string $country_code;

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
        $this->city_locality = $location_data['city_locality'] ?? null;
        $this->state_province = $location_data['state_province'] ?? null;
        $this->postal_code = $location_data['postal_code'] ?? null;
        $this->country_code = $location_data['country_code'] ?? null;
        $this->latitude = $location_data['latitude'] ?? null;
        $this->longitude = $location_data['longitude'] ?? null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
