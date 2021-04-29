<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Util;

/**
 * Class Carrier - Immutable carrier object.
 *
 * @package ShipEngine\Service\Carriers
 */
final class Carrier implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The common carrier/provider name: `FedEx`
     *
     * @var string
     */
    private string $carrier_name;

    /**
     * The actual **carrier_code** that ShipEngine API uses: `fedex`
     *
     * @var string
     */
    private string $carrier_code;

    /**
     * Carrier constructor.
     *
     * @param string $carrier_name
     * @param string $carrier_code
     */
    public function __construct(string $carrier_name, string $carrier_code)
    {
        $this->carrier_name = $carrier_name;
        $this->carrier_code = $carrier_code;
//        The below is for use in case we use a POPO here instead of args
//        $this->carrier_name = $carrier_info['carrier_name'];
//        $this->carrier_code = $carrier_info['carrier_code'];
    }

    /**
     * {
     *  "carrier_name": "FedEx",
     *  "carrier_code": "fedex"
     * }
     */
    public function jsonSerialize()
    {
        return [
            'carrier_name' => $this->carrier_name,
            'carrier_code' => $this->carrier_code
        ];
    }
}
