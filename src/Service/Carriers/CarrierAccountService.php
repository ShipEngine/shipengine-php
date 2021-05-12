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

    /**
     * Get all carrier accounts for a given ShipEngine account.
     *
     * @param string|null $params
     * @return array
     * @throws ClientExceptionInterface
     */
    public static function fetchCarrierAccounts(ShipEngineConfig $config, ?string $params = null): array
    {
        $client = new ShipEngineClient();
        $config = $config->merge();

        if (count(self::$accounts) > 0) {
            return self::$accounts;
        }

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
