<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\ShipEngineClient;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Service\ShipEngineConfig;

/**
 * Class CarrierAccountService
 * @package ShipEngine\Service\Carriers
 */
final class CarrierAccountService
{
    public function fetchCarrierAccounts(ShipEngineConfig $config): CarrierAccount
    {
        $client = new ShipEngineClient();
        $api_response = $client->getRequest('/carriers', $config);
        return new CarrierAccount($api_response);
    }
}
