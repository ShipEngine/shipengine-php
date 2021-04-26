<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

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
    const FEDEX = 'fedex';

    /**
     * UPS - United Parcel Service
     *
     * @link https://www.ups.com/us/en/about/sites.page
     */
    const UPS = 'ups';

    /**
     * USPS - United State Postal Service
     *
     * @link https://www.stamps.com/
     */
    const USPS = 'stamps_com';  // TODO: confirm the above link with James
}
