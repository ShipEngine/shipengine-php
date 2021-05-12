<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

/**
 * This class contains constants that are the common names for various carriers.
 *
 * @package ShipEngine\Util\Constants
 */
final class CarrierNames
{
    //TODO: move carrier name check logic into a getCarrierNames() getter
    /**
     * Common name for FEDEX = FedEx
     */
    public const FEDEX = 'FedEx';

    /**
     * Common name for UPS - United Parcel Service
     */
    public const UPS = 'United Parcel Service';

    /**
     * Common name for USPS - U.S. Postal Service
     */
    public const USPS = 'U.S. Postal Service';

    /**
     * Stamps.com - provider of USPS Services
     */
    public const STAMPS_COM = 'Stamps.com';
}
