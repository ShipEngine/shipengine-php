<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 *
 */
final class Address
{
    use Model;
    
    private array $street;
    private string $city_locality;
    private string $state_province;
    private string $postal_code;
    private string $country;
    private ?bool $residential;

    public function __construct(
        array $street,
        string $city_locality,
        string $state_province,
        string $postal_code,
        string $country,
        bool $residential = null
    ) {
        $this->street = $street;
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country = $country;
        $this->residential = $residential;
    }
    
    public function isResidential(): bool
    {
        if (isset($this->residential)) {
            return $this->residential;
        }
        return false;
    }

    public function __toString(): string
    {
    }
}
