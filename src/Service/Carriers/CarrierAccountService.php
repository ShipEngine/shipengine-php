<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Class CarrierAccountService
 *
 * @package ShipEngine\Service\Carriers
 */
final class CarrierAccountService
{
    /**
     * Cached list of carrier accounts if any are present.
     *
     * @var array
     */
    public static array $accounts = array();

    public static function fetchCarrierAccounts(ShipEngineConfig $config, ?string $carrierCode = null): array
    {
        $client = new ShipEngineClient();

        if (isset($carrierCode)) {
            $apiResponse = $client->request(
                RPCMethods::LIST_CARRIERS,
                $config,
                array('carrierCode' => $carrierCode)
            );
        } else {
            $apiResponse = $client->request(
                RPCMethods::LIST_CARRIERS,
                $config
            );
        }

        $accounts = $apiResponse['result']['carrierAccounts'];
        self::$accounts = array();
        foreach ($accounts as $account) {
            $carrierAccount = new CarrierAccount($account);
            self::$accounts[] = $carrierAccount;
        }

        return self::$accounts;
    }


    public static function fetchCachedCarrierAccounts(ShipEngineConfig $config, ?string $carrier): array
    {
        $accounts = self::getCachedAccounts($carrier);

        if (count(self::$accounts) > 0) {
            return $accounts;
        }

        return self::fetchCarrierAccounts($config, $carrier);
    }

    private static function getCachedAccounts(?string $carrierCode): array
    {
        if (!isset($carrierCode)) {
            return self::$accounts;
        }

        $accounts = array();

        foreach (self::$accounts as $account) {
            if ($account->carrier->code === $carrierCode) {
                $accounts[] = $account;
            }
        }
        return $accounts;
    }
}
