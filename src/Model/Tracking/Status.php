<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

/**
 * Shipment status.
 *
 * NOTE: [status codes](https://www.shipengine.com/docs/tracking/#tracking-status-codes).
 */
final class Status
{
    const ACCEPTED = "ACCEPTED";
    const ATTEMPTED_DELIVERY = "ATTEMPTED DELIVERY";
    const DELIVERED = "DELIVERED";
    const EXCEPTION = "EXCEPTION";
    const IN_TRANSIT = "IN TRANSIT";
    const NOT_YET_IN_SYSTEM = "NOT YET IN SYSTEM";
    const UNKNOWN = "UNKNOWN";
}
