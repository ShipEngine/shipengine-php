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
     * @var bool
     */
    private bool $valid;

    /**
     * @var bool
     */
    private bool $residential;

    /**
     * @var array
     */
    private array $messages;


    /**
     * Address Type constructor.
     *
     * @param array $street
     * @param string $city_locality
     * @param string $state_province
     * @param string $postal_code
     * @param string $country_code
     * @param bool $valid
     * @param bool $residential
     * @param array $messages
     */
    public function __construct(
        array $street,
        string $city_locality,
        string $state_province,
        string $postal_code,
        string $country_code,
        bool $valid,
        bool $residential,
        array $messages
    ) {
        $this->$valid = $valid;
        $this->messages = $messages;
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

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'valid' => $this->valid,
            'messages' => $this->messages,
            'street' => $this->street,
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'residential' => $this->residential,

        ]);
    }
}
