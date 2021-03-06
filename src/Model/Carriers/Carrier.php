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
        $upperCaseCode = strtoupper($code);

        if (Carriers::doesCarrierExist($upperCaseCode) === true) {
            $this->name = CarrierNames::getCarrierName($upperCaseCode);
        } else {
            throw new InvalidFieldValueException(
                'carrierAccount',
                "Carrier [$code] is currently not supported by the SDK",
                $code
            );
        }
    }

    /**
     * ```json5
     * {
     *  "name": "FedEx",
     *  "code": "fedex"
     * }
     * ```
     *
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'code' => $this->code
        ];
    }
}
