<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

/**
 * Shipment status.
 *
 * Tracking Status Codes: [status codes](https://www.shipengine.com/docs/tracking/#tracking-status-codes).
 * @link https://www.shipengine.com/docs/tracking/#tracking-status-codes
 */
final class TrackingStatus
{
    public const ACCEPTED = "ACCEPTED";
    public const ATTEMPTED_DELIVERY = "ATTEMPTED DELIVERY";
    public const DELIVERED = "DELIVERED";
    public const EXCEPTION = "EXCEPTION";
    public const IN_TRANSIT = "IN TRANSIT";
    public const NOT_YET_IN_SYSTEM = "NOT YET IN SYSTEM";
    public const UNKNOWN = "UNKNOWN";
}
