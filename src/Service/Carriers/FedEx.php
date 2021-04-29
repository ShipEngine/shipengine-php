<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class FedEx - Immutable carrier object representing FedEx (Federal Express).
 *
 * @package ShipEngine\Service\Carriers
 */
final class FedEx implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'FedEx';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `fedex`
     *
     * @var string
     */
    private string $carrier_code = Carriers::FEDEX;

    /**
     * {
     *  "carrier_name": "FedEx",
     *  "carrier_code": "fedex"
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
