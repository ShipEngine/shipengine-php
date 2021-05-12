<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use Psr\Http\Client\ClientExceptionInterface;
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

    public static function fetchCachedCarrierAccounts(ShipEngineConfig $config, ?string $carrier): array
    {
        //TODO: DEBUG ME
        $accounts = array();
        //TODO: debug the lint on the method name
        //Iterate through $accounts to only get the ones that match params and only return those.
        // IF params are provided else return $accounts.
        if (count(self::$accounts) > 0) {
            foreach (self::$accounts as $account) {
                if ($account->carrier->code === $carrier) {
                    $accounts = $account;
                } else {
                    self::$accounts = self::fetchCarrierAccounts($config, $carrier);
                    $accounts = self::$accounts;
                }
            }
        } else {
            self::$accounts = self::fetchCarrierAccounts($config, $carrier);
            $accounts = self::$accounts;
        }
        //TODO: add comments
        return $accounts;
    }

    public static function fetchCarrierAccounts(ShipEngineConfig $config, ?string $params = null): array
    {
        $client = new ShipEngineClient();

        if (isset($params)) {
            $apiResponse = $client->request(
                RPCMethods::LIST_CARRIERS,
                $config,
                array('carrierCode' => $params)
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
}
