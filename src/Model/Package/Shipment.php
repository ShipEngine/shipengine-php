<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use DateTime;
use ShipEngine\Model\Carriers\Carrier;
use ShipEngine\Model\Carriers\CarrierAccount;

final class Shipment implements \JsonSerializable
{
    public ?string $shipment_id;

    public ?string $carrier_id;

    public ?CarrierAccount $carrier_account;

    public Carrier $carrier;

    public DateTime $estimated_delivery_date;

    public string $actual_delivery_date;

    public function __construct(array $shipment)
    {
        $this->shipment_id = $shipment['shipment_id'] ?? null;
        $this->carrier_id = $shipment['shipment_id'] ?? null;
        $this->carrier_account = $shipment['carrier_account'] ?? null;
        $this->carrier = $shipment['carrier'];
        $this->estimated_delivery_date = $shipment['estimated_delivery'];
        $this->actual_delivery_date = $shipment['actual_delivery_date'];
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
