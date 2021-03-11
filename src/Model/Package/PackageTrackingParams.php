<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * PackageTrackingParams Type to be passed into the *PackageTrackService*.
 *
 * @package ShipEngine\Model\Package
 */
final class PackageTrackingParams
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
     * @param $carrier_code
     * @param $tracking_number
     */
    public function __construct(
     $carrier_code,
     $tracking_number
    ) {
        $this->carrier_code = $carrier_code;
        $this->tracking_number = $tracking_number;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'carrier_code' => $this->carrier_code,
            'tracking_number' => $this->tracking_number
        ]);
    }
}
