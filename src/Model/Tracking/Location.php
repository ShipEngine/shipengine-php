<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

use ShipEngine\Util;

/**
 * A location.
 *
 * @property ?string $city_locality
 * @property ?string $state_province
 * @property ?string $postal_code
 * @property ?string $country
 * @property ?float $latitude
 * @property ?float $longitude
 */
final class Location
{
    use Util\Getters;
    
    private ?string $city_locality;
    private ?string $state_province;
    private ?string $postal_code;
    private ?string $country;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(
        ?string $city_locality = null,
        ?string $state_province = null,
        ?string $postal_code = null,
        ?string $country = null,
        ?float $latitude = null,
        ?float $longitude = null
    ) {
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country = $country_code;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
