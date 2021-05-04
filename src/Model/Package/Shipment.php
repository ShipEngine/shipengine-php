<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Model\Carriers\Carrier;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Util\IsoString;

final class Shipment implements \JsonSerializable
{
    public ?string $shipment_id;

    public ?string $carrier_id;

    public ?CarrierAccount $carrier_account;

    public Carrier $carrier;

    public IsoString $estimated_delivery_date;

    public IsoString $actual_delivery_date;

    public function __construct(array $shipment, IsoString $actual_delivery_date)
    {
        $this->shipment_id = null ?? $shipment['shipment_id'];
        $this->carrier_id = null ?? $shipment['carrier_id'];
        $this->carrier_account = isset($shipment['carrier_account']) ?
            new CarrierAccount($shipment['carrier_account']) :
            null;
        $this->carrier = new Carrier($shipment['carrier_code']);
        $this->estimated_delivery_date = new IsoString($shipment['estimated_delivery']);
        $this->actual_delivery_date = $actual_delivery_date;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'shipment_id' => $this->shipment_id,
            'carrier_id' => $this->carrier_id,
            'carrier_account' => $this->carrier_account,
            'carrier' => $this->carrier,
            'estimated_delivery' => $this->estimated_delivery_date,
            'actual_delivery' => $this->actual_delivery_date,
        ];
    }
}
