<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * Class TrackingQuery - This Type is used as an argument in the `TrackPackageService` methods.
 *
 * @package ShipEngine\Model\Package
 */
final class TrackingQuery implements \JsonSerializable
{
    /**
     * @var string|null
     */
    public ?string $carrier_code;

    /**
     * @var string|null
     */
    public ?string $tracking_number;

    /**
     * TrackingQuery constructor.
     *
     * @param string|null $carrier_code
     * @param string|null $tracking_number
     */
    public function __construct(string $carrier_code = null, string $tracking_number = null)
    {
        $this->carrier_code = $carrier_code;
        $this->tracking_number = $tracking_number;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'carrier_code' => $this->carrier_code,
            'tracking_number' => $this->tracking_number,
        ];
    }
}
