<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class CarrierAccount - This class represents a given Carrier Account e.g. FedEx, UPS, USPS.
 *
 * @package ShipEngine\Model\Carriers
 */
final class CarrierAccount implements \JsonSerializable
{
    /**
     * An immutable object that will represent a carrier account.
     *
     * @var Carrier
     */
    public Carrier $carrier;

    /**
     * The unique ID that is associated with the current carrier account.
     *
     * @var string
     */
    public string $accountId;

    /**
     * The account number of the current carrier account.
     *
     * @var string
     */
    public string $accountNumber;

    /**
     * The account name of the current carrier account.
     *
     * @var string
     */
    public string $name;

    /**
     * CarrierAccount constructor. This class contains account information such as
     * the carrier/provider, accountId, account number, and account name.
     *
     * @param array $accountInformation
     */
    public function __construct(array $accountInformation)
    {
        $this->setCarrierAccount($accountInformation);
        $this->accountId = $accountInformation['accountID'];
        $this->accountNumber = $accountInformation['accountNumber'];
    }

    /**
     * Instantiate an immutable carrier class based on the `carrier` key
     * and sets `$this->carrier` based on it's value.
     *
     * @param array $accountInformation
     */
    private function setCarrierAccount(array $accountInformation): void
    {
        if (array_key_exists('carrierCode', $accountInformation)) {
            $carrierCode = $accountInformation['carrierCode'];
            $upperCaseCarrierCode = strtoupper($accountInformation['carrierCode']);

            if (Carriers::doesCarrierExist($upperCaseCarrierCode) === true) {
                $this->carrier = new Carrier(
                    $carrierCode
                );

                $this->name = $this->carrier->name;
            } else {
                throw new InvalidFieldValueException(
                    'carrier',
                    "Carrier [$carrierCode] is currently not supported.",
                    $carrierCode
                );
            }
        }
    }

    /**
     * {
     *  "carrier": {
     *      "carrier_name": "FedEx",
     *      "carrierCode": "fedex"
     * },
     *  "accountId": "car_a09a8jsfd09wjzxcs9dfyha",
     *  "accountNumber": "SDF987",
     *  "name": "ShipEngine FedEx Account"
     * }
     *
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>
     */
    public function jsonSerialize()
    {
        return [
            'carrier' => $this->carrier,
            'accountId' => $this->accountId,
            'accountNumber' => $this->accountNumber,
            'name' => $this->name,
        ];
    }
}
