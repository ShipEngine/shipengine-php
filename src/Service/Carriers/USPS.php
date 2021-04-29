<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class USPS - Immutable carrier object representing USPS (U.S. Postal Service).
 *
 * @package ShipEngine\Service\Carriers
 */
final class USPS implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'U.S. Postal Service';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `stamps_com`
     *
     * @var string
     */
    private string $carrier_code = Carriers::USPS;

    /**
     * {
     *  "carrier_name": "U.S. Postal Service",
     *  "carrier_code": "stamps_com"
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
