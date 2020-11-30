<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 *
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
