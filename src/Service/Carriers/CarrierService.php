<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Class ListCarriersService
 *
 * @package ShipEngine\Service\Carriers
 */
final class CarrierService
{
    /**
     * Cached list of carrier accounts if any are present.
     *
     * @var array
     */
    public static array $accounts = array();

    public static function listCarriers(ShipEngineConfig $config): array
    {
        $client = new ShipEngineClient();

        $apiResponse = $client->get(
            'v1/carriers'
            $config,
        );

        $accounts = $apiResponse['result'];
        self::$accounts = array();
        foreach ($accounts as $account) {
            $carrierAccount = new CarrierAccount($account);
            self::$accounts[] = $carrierAccount;
        }

        return self::$accounts;
    }
}
