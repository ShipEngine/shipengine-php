<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * Address Class to be used as an Address Type.
 *
 * @package ShipEngine\Model\Address
 * @property array $street
 * @property string $city_locality
 * @property string $state_province
 * @property string $postal_code
 * @property string $country_code
 * @property bool|null $residential
 */
final class Address
{
    use Util\Getters;

    /**
     * @var array
     */
    private array $street;

    /**
     * @var string
     */
    private string $city_locality;

    /**
     * @var string
     */
    private string $state_province;

    /**
     * @var string
     */
    private string $postal_code;

    /**
     * @var string
     */
    private string $country_code;

    /**
     * @var bool|null
     */
    private ?bool $residential;

    /**
     * Address Type constructor.
     * @param array $street
     * @param string $city_locality
     * @param string $state_province
     * @param string $postal_code
     * @param string $country_code
     * @param bool|null $residential
     */
    public function __construct(
        array $street,
        string $city_locality,
        string $state_province,
        string $postal_code,
        string $country_code,
        ?bool $residential
    ) {
        $this->street = $street;
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country_code = $country_code;
        $this->residential = $residential;
    }

    /**
     * @return bool
     */
    public function isResidential(): bool
    {
        if (isset($this->residential)) {
            return $this->residential;
        }
        return false;
    }
}
