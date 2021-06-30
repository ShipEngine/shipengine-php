<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * Class Package
 *
 * @package ShipEngine\Model\Package
 */
final class Package implements \JsonSerializable
{
    /**
     * The unique ID that corresponds to the current package.
     *
     * @var string|null
     */
    public ?string $packageId;

    /**
     * Weight of the given shipment.
     *
     * @var array|null
     */
    public ?array $weight;

    /**
     * Dimensions of the given shipment.
     *
     * @var array|null
     */
    public ?array $dimensions;

    /**
     * The tracking number of a given shipment. This number helps is obtaining tracking updates for a
     * specific shipment.
     *
     * @var string|null
     */
    public ?string $trackingNumber;

    /**
     * The tracking URL to the carrier site with tracking information on your shipment.
     *
     * @var string|null
     */
    public ?string $trackingUrl;

    /**
     * Package Class constructor. This is an object containing package information
     * for a given shipment.
     *
     * @param array $package
     */
    public function __construct(array $package)
    {
        $this->packageId = isset($package['packageId']) ? $package['packageId'] : null;
        $this->weight = isset($package['weight']) ? $package['weight'] : null;
        $this->dimensions = isset($package['dimensions']) ? $package['dimensions'] : null;
        $this->trackingUrl = isset($package['trackingUrl']) ? $package['trackingUrl'] : null;
        $this->trackingNumber = $package['trackingNumber'] ? $package['trackingNumber'] : null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
          'packageId' => $this->packageId,
          'weight' => $this->weight,
          'dimensions' => $this->dimensions,
          'trackingNumber' => $this->trackingNumber,
          'trackingUrl' => $this->trackingUrl,
        ];
    }
}
