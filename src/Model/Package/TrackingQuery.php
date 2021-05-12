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
    public ?string $carrierCode;

    /**
     * @var string|null
     */
    public ?string $trackingNumber;

    /**
     * TrackingQuery constructor.
     *
     * @param string|null $carrierCode
     * @param string|null $trackingNumber
     */
    public function __construct(string $carrierCode = null, string $trackingNumber = null)
    {
        $this->carrierCode = $carrierCode;
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'carrierCode' => $this->carrierCode,
            'trackingNumber' => $this->trackingNumber,
        ];
    }
}
