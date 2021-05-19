<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

use ShipEngine\Message\ShipEngineException;

/**
 * This class contains constants that are the common names for various carriers.
 *
 * @package ShipEngine\Util\Constants
 */
final class CarrierNames
{
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

    /**
     * Verify that the passed in uppercase carrier name is a constant that exists on this class.
     *
     * @param string $upperCaseCarrierCode
     * @return bool
     */
    public static function doesCarrierNameExist(string $upperCaseCarrierCode): bool
    {
        return defined("ShipEngine\Util\Constants\CarrierNames::$upperCaseCarrierCode");
    }

    /**
     * Verify that the passed in uppercase carrier code is a constant that exists on this class
     * and returns its value.
     *
     * @param string $upperCaseCarrierCode
     * @return mixed
     */
    public static function getCarrierName(string $upperCaseCarrierCode)
    {
        if (self::doesCarrierNameExist($upperCaseCarrierCode) === true) {
            return constant("ShipEngine\Util\Constants\CarrierNames::$upperCaseCarrierCode");
        }

        throw new ShipEngineException(
            "CarrierName [$upperCaseCarrierCode] is not a constant in this class object."
        );
    }
}
