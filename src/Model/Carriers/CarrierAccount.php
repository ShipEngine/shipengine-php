<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Util\Constants\CarrierNames;
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
    public Carrier $carrier_account;

    /**
     * The unique ID that is associated with the current carrier account.
     *
     * @var string
     */
    public string $account_id;

    /**
     * The account number of the current carrier account.
     *
     * @var string
     */
    public string $account_number;

    /**
     * The account name of the current carrier account.
     *
     * @var string
     */
    public string $name;

    /**
     * CarrierAccount constructor. This class contains account information such as
     * the carrier/provider, account_id, account number, and account name.
     *
     * @param array $account_information
     */
    public function __construct(array $account_information)
    {
        $this->setCarrierAccount($account_information);
        $this->account_id = $account_information['id'];
        $this->account_number = $account_information['account_number'];
        $this->name = $account_information['name'];
    }

    /**
     * Instantiate an immutable carrier class based on the `carrier_account` key
     * and sets `$this->carrier_account` based on it's value.
     *
     * @param array $account_information
     */
    private function setCarrierAccount(array $account_information)
    {
        if (array_key_exists('carrier_account', $account_information)) {
            $account = $account_information['carrier_account'];
            switch ($account) {
                case Carriers::FEDEX:
                    $this->carrier_account = new Carrier(
                        CarrierNames::FEDEX,
                        Carriers::FEDEX
                    );
                    break;
                case Carriers::UPS:
                    $this->carrier_account = new Carrier(
                        CarrierNames::UPS,
                        Carriers::UPS
                    );
                    break;
                case Carriers::USPS:
                    $this->carrier_account = new Carrier(
                        CarrierNames::USPS,
                        Carriers::USPS
                    );
                    break;
                default:
                    throw new InvalidFieldValueException(
                        'carrier_account',
                        "Carrier [$account] is currently not supported.",
                        $account
                    );
            }
        }
    }

    /**
     * {
     *  "carrier_account": {
     *      "carrier_name": "FedEx",
     *      "carrier_code": "fedex"
     * },
     *  "account_id": "car_a09a8jsfd09wjzxcs9dfyha",
     *  "account_number": "SDF987",
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
            'carrier_account' => $this->carrier_account,
            'account_id' => $this->account_id,
            'account_number' => $this->account_number,
            'name' => $this->name,
        ];
    }
}
