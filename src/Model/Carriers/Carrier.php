<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Util\Constants\CarrierNames;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class Carrier - Immutable carrier object.
 *
 * @package ShipEngine\Service\Carriers
 */
final class Carrier implements \JsonSerializable
{
    /**
     * The common carrier/provider name: `FedEx`
     *
     * @var string
     */
    public string $name;

    /**
     * The actual **code** that ShipEngine API uses: `fedex`
     *
     * @var string
     */
    public string $code;

    /**
     * Carrier constructor.
     *
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;

        switch ($code) {
            case Carriers::FEDEX:
                $this->name = CarrierNames::FEDEX;
                break;
            case Carriers::UPS:
                $this->name = CarrierNames::UPS;
                break;
            case Carriers::USPS:
                $this->name = CarrierNames::USPS;
                break;
            default:
                throw new InvalidFieldValueException(
                    'carrier_account',
                    "Carrier [$code] is currently not supported by the SDK",
                    $code
                );
        };

        $this->code = $code;
//        The below is for use in case we use a POPO here instead of args
//        $this->name = $carrier_info['name'];
//        $this->code = $carrier_info['code'];
    }

    /**
     * {
     *  "name": "FedEx",
     *  "code": "fedex"
     * }
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'code' => $this->code
        ];
    }
}
