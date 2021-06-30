<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Carriers\Carrier;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Service\Carriers\CarrierAccountService;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\IsoString;

/**
 * Class Shipment
 *
 * @package ShipEngine\Model\Package
 */
final class Shipment implements \JsonSerializable
{
    /**
     * @var ShipEngineConfig
     */
    protected ShipEngineConfig $config;

    /**
     * @var string|null
     */
    public ?string $shipmentId;

    /**
     * @var string|null
     */
    public ?string $accountId;

    /**
     * @var CarrierAccount|null
     */
    public ?CarrierAccount $carrierAccount;

    /**
     * Returns the carrier account that matches the carrier account referenced by the Tracking response, in
     * the form of a **CarrierAccount** object.
     */
    private function getCarrierAccount(string $carrier, string $accountId)
    {
        $target_carrier = array();
        $carrierAccounts = CarrierAccountService::fetchCachedCarrierAccounts($this->config, $carrier);

        foreach ($carrierAccounts as $account) {
            if ($accountId === $account->accountId) {
                $target_carrier[] = $account;
                return $target_carrier[0];
            }

            throw new ShipEngineException(
                "accountID [$accountId] doesn't match any of the accounts connected to your ShipEngine Account"
            );
        }
        return $target_carrier;
    }

    /**
     * @var Carrier
     */
    public Carrier $carrier;

    /**
     * @var IsoString
     */
    public IsoString $estimatedDeliveryDate;

    /**
     * @var IsoString
     */
    public IsoString $actualDeliveryDate;

    /**
     * Shipment constructor.
     * @param array $shipment
     * @param IsoString $actualDeliveryDate
     * @param ShipEngineConfig $config
     */
    public function __construct(array $shipment, IsoString $actualDeliveryDate, ShipEngineConfig $config)
    {
        $this->config = $config;
        $this->shipmentId = isset($shipment['shipmentId']) ? $shipment['shipmentId'] : null;
        $this->accountId = isset($shipment['carrierAccountId']) ? $shipment['carrierAccountId'] : null;


        $this->carrierAccount = isset($this->accountId) ?
            $this->getCarrierAccount($shipment['carrierCode'], $this->accountId) :
            null;

        $this->carrier = isset($this->carrierAccount) ?
            $this->carrierAccount->carrier :
            new Carrier($shipment['carrierCode']);
        $this->estimatedDeliveryDate = new IsoString($shipment['estimatedDelivery']);
        $this->actualDeliveryDate = $actualDeliveryDate;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'shipmentId' => $this->shipmentId,
            'carrierAccountId' => $this->accountId,
            'carrierAccount' => $this->carrierAccount,
            'carrier' => $this->carrier,
            'estimatedDeliveryDate' => (string)$this->estimatedDeliveryDate,
            'actualDeliveryDate' => (string)$this->actualDeliveryDate,
        ];
    }

    /**
     * This is a helper method to unset and remove **$this->config** from the **print_r()** or **var_dump()**
     * output since it's a large object, and is only on **$this** object for internal use.
     *
     * @return array
     */
    public function __debugInfo()
    {
        $result = get_object_vars($this);
        unset($result['config']);
        return $result;
    }
}
