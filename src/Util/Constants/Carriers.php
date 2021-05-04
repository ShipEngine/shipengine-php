<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

/**
 * Carriers that are supported by ShipEngine API.
 */
final class Carriers
{
    /**
     * FedEx - Federal Express
     *
     * @link https://www.fedex.com/en-us/home.html
     */
    public const FEDEX = 'FEDEX';

    /**
     * UPS - United Parcel Service
     *
     * @link https://www.ups.com/us/en/about/sites.page
     */
    public const UPS = 'UPS';

    /**
     * USPS - United State Postal Service
     *
     * @link https://www.stamps.com/
     */
    public const USPS = 'STAMPS COM';  // TODO: confirm the above link with James
}
