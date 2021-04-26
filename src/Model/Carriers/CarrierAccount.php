<?php declare(strict_types=1);

namespace ShipEngine\Model\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;
use ShipEngine\Service\Carriers\FedEx;
use ShipEngine\Service\Carriers\UPS;
use ShipEngine\Service\Carriers\USPS;
use ShipEngine\Message\InvalidFieldValueException;

/**
 * Class CarrierAccount - This class represents a given Carrier Account e.g. FedEx, UPS, USPS.
 *
 * @package ShipEngine\Model\Carriers
 */
final class CarrierAccount
{
    use Util\Getters;

    /**
     * An immutable object that will represent a carrier account.
     *
     * @var object
     */
    private object $carrier_account;

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
            if ($carrier_account === Carriers::FEDEX) {
                $this->carrier_account = new FedEx();
            } elseif ($carrier_account === Carriers::UPS) {
                $this->carrier_account = new UPS();
            } elseif ($carrier_account === Carriers::USPS) {
                $this->carrier_account = new USPS();
            } else {
                throw new InvalidFieldValueException(
                    'carrier_account',
                    'Carrier currently not supported.',  //TODO: temporary generic error message
                    $carrier_account
                );
            }
        }
    }
}
