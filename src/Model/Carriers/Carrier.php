<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

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
     * @param string $name
     * @param string $code
     */
    public function __construct(string $name, string $code)
    {
        $this->name = $name;
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
