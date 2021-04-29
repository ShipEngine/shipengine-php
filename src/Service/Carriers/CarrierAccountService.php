<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\ShipEngineClient;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\ShipEngineConfig;

/**
 * Class CarrierAccountService
 *
 * @package ShipEngine\Service\Carriers
 */
final class CarrierAccountService
{
    /**
     * Get all carrier accounts for a given ShipEngine account.
     *
     * @param ShipEngineConfig $config
     * @return CarrierAccount
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchCarrierAccounts(ShipEngineConfig $config): CarrierAccount
    {
        $client = new ShipEngineClient();
        $api_response = $client->getRequest('/carriers', $config);
        return new CarrierAccount($api_response);
    }
}
