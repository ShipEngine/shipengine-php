<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;
use ShipEngine\Util;

/**
 * Class CarrierAccountService
 *
 * @package ShipEngine\Service\Carriers
 */
final class CarrierAccountService
{
    use Util\Getters;

    /**
     * Cached list of carrier accounts if any are present.
     *
     * @var array
     */
    private array $accounts;

    // TODO: implement 'caching' to store carrier accounts. - in TrackPackageService
    /**
     * Get all carrier accounts for a given ShipEngine account.
     *
     * @param ShipEngineConfig $config
     * @return array
     * @throws ClientExceptionInterface
     */
    public function fetchCarrierAccounts(ShipEngineConfig $config): array
    {
        $client = new ShipEngineClient();
        $api_response = $client->request(
            RPCMethods::LIST_CARRIER_ACCOUNTS,
            $config
        );

        $accounts = $api_response['result']['carrier_accounts'];
        $this->accounts = array();
        foreach ($accounts as $account) {
            $carrier_account = new CarrierAccount($account);
            array_push($this->accounts, $carrier_account);
        }

        return $this->accounts;
    }
}
