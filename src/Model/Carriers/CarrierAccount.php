<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Util;
use ShipEngine\Util\Constants\CarrierNames;
use ShipEngine\Util\Constants\Carriers;

/**
 * Class CarrierAccount - This class represents a given Carrier Account e.g. FedEx, UPS, USPS.
 *
 * @package ShipEngine\Model\Carriers
 */
final class CarrierAccount implements \JsonSerializable
{
    use Util\Getters;

    /**
     * An immutable object that will represent a carrier account.
     *
     * @var Carrier
     */
    private Carrier $carrier_account;

    /**
     * The unique ID that is associated with the current request to ShipEngine API
     * for address validation.
     *
     * @var string
     */
    private string $request_id;

    /**
     * The account number of the current carrier account.
     *
     * @var string
     */
    private string $account_number;

    /**
     * The account name of the current carrier account.
     *
     * @var string
     */
    private string $account_name;

    /**
     * CarrierAccount constructor. This class contains account information such as
     * the carrier/provider, request_id, account number, and account name.
     *
     * @param array $account_information
     */
    public function __construct(array $account_information)
    {
        $this->setCarrierAccount($account_information);
        $this->request_id = $account_information['id'];
        $this->account_number = $account_information['account_number'];
        $this->account_name = $account_information['account_name'];
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
            $carrier_account = $account_information['carrier_account'];
            switch ($carrier_account) {
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
                        "Carrier [$carrier_account] is currently not supported.",
                        $carrier_account
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
     *  "request_id": "car_a09a8jsfd09wjzxcs9dfyha",
     *  "account_number": "SDF987",
     *  "account_name": "ShipEngine FedEx Account"
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
            'request_id' => $this->request_id,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
        ];
    }
}
