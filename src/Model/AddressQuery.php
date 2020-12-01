<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 * A query representing a possible \ShipEngine\Model\Address.
 *
 * @property array $city_locality
 * @property ?string $city_locality
 * @property ?string $state_province
 * @property ?string $postal_code
 * @property ?string $country
 */
final class AddressQuery implements \JsonSerializable
{
    use Getters;
    
    private array $street;
    private ?string $city_locality;
    private ?string $state_province;
    private ?string $postal_code;
    private ?string $country;

    public function __construct(
        array $street,
        string $city_locality = null,
        string $state_province = null,
        string $postal_code = null,
        string $country = null
    ) {
        $this->street = $street;
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country = $country;
    }

    /**
     * Serialize into JSON.
     */
    public function jsonSerialize()
    {
        return [
            'street' => implode(' ', $this->street),
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country' => $this->country
        ];
    }
}
