<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * `PackageTrackingParams` Type to be passed into the *PackageTrackService*.
 *
 * @package ShipEngine\Model\Package
 * @property string $carrier_code
 * @property string $tracking_number
 */
final class PackageTrackingParams implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $carrier_code;

    /**
     * @var string
     */
    private string $tracking_number;

    /**
     * PackageTrackingParams constructor.
     * @param string $carrier_code
     * @param string $tracking_number
     */
    public function __construct(
        string $carrier_code,
        string $tracking_number
    ) {
        $this->carrier_code = $carrier_code;
        $this->tracking_number = $tracking_number;
    }

    /**
     * Return a JsonSerialized string representation of the `PackageTrackingParams` Type.
     *
     * <code>
     * {
     *  "carrier_code": "ups",
     *  "tracking_number": "abc123"
     * }
     * </code>
     */
    public function jsonSerialize()
    {
        return [
            'carrier_code' => $this->carrier_code,
            'tracking_number' => $this->tracking_number
        ];
    }
}
