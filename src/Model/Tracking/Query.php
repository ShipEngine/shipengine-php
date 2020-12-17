<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking\Query;

use ShipEngine\Util;

/**
 * A query to track a `Shipment`.
 */
final class Query
{
    use Util\Getters;

    private string $carrier_code;
    private string $tracking_number;

    public function __construct(string $carrier_code, string $tracking_number)
    {
        $this->carrier_code = $carrier_code;
        $this->tracking_number = $tracking_number;
    }
}
