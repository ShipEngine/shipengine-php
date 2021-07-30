<?php declare(strict_types=1);

namespace ShipEngine\Carriers;

use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Class ListCarriers
 *
 * @package ShipEngine\ListCarriers
 */
final class ListCarriers
{
    /**
     * Cached list of carrier accounts if any are present.
     *
     * @var array
     */
    public static array $accounts = array();

    public static function call(ShipEngineConfig $config): array
    {

        $client = new ShipEngineClient();
        $apiResponse = $client->get(
            'v1/carriers',
            $config,
        );

        $carriers = $apiResponse['carriers'];
        self::$carriers = array();
        foreach ($carriers as $carrier) {
            self::$carriers[] = new CarrierAccount($carrier);
        }

        return self::$carriers;
    }
}
