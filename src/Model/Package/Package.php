<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Util;
use GuzzleHttp\Psr7\Uri;

/**
 * Class Package
 *
 * @package ShipEngine\Model\Package
 */
final class Package implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The unique ID that corresponds to the current package.
     *
     * @var string|null
     */
    private ?string $package_id;

    /**
     * Weight of the given shipment.
     *
     * @var string|null
     */
    private ?string $weight;

    /**
     * Dimensions of the given shipment.
     *
     * @var array|null
     */
    private ?array $dimensions;

    /**
     * The tracking number of a given shipment. This number helps is obtaining tracking updates for a
     * specific shipment.
     *
     * @var string
     */
    private string $tracking_number;

    /**
     * The tracking URL to the carrier site with tracking information on your shipment.
     *
     * @var Uri
     */
    private Uri $tracking_url;

    /**
     * Package Class constructor. This is an object containing package information
     * for a given shipment.
     *
     * @param array $package
     */
    public function __construct(array $package)
    {
        $this->package_id = $pacakge['package_id'] ?? null;
        $this->weight = $package['weight'] ?? null;
        $this->dimensions = $package['dimensions'] ?? null;
        $this->tracking_number = $package['tracking_number'];
        $this->tracking_url = $package['tracking_url'];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
          'package_id' => $this->package_id,
          'weight' => $this->weight,
          'dimensions' => $this->dimensions,
          'tracking_number' => $this->tracking_number,
          'tracking_url' => $this->tracking_url,
        ];
    }
}
