<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class UPS - Immutable carrier object representing UPS (United Parcel Service).
 *
 * @package ShipEngine\Service\Carriers
 */
final class UPS implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'United Parcel Service';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `ups`
     *
     * @var string
     */
    private string $carrier_code = Carriers::UPS;

    /**
     * {
     *  "carrier_name": "United Parcel Service",
     *  "carrier_code": "ups"
     * }
     *
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>
     */
    public function jsonSerialize()
    {
        return [
            'carrier_name' => $this->carrier_name,
            'carrier_code' => $this->carrier_code
        ];
    }
}
