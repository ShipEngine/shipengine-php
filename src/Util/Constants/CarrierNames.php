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
     * DHL Express
     */
    public const DHL_EXPRESS = 'DHL Express';

    /**
     * DHL ECommerce
     */
    public const DHL_GLOBAL_MAIL = 'DHL ECommerce';

    /**
     * Canada Post
     */
    public const CANADA_POST = 'Canada Post';

    /**
     * Australia Post
     */
    public const AUSTRALIA_POST = 'Australia Post';

    /**
     * First Mile
     */
    public const FIRSTMILE = 'First Mile';

    /**
     * Asendia
     */
    public const ASENDIA = 'Asendia';

    /**
     * OnTrac
     */
    public const ONTRAC = 'OnTrac';

    /**
     * APC
     */
    public const APC = 'APC';

    /**
     * Newgistics
     */
    public const NEWGISTICS = 'Newgistics';

    /**
     * Globegistics
     */
    public const GLOBEGISTICS = 'Globegistics';

    /**
     * RR Donnelley
     */
    public const RR_DONNELLEY = 'RR Donnelley';

    /**
     * IMEX
     */
    public const IMEX = 'IMEX';

    /**
     * Access Worldwide
     */
    public const ACCESS_WORLDWIDE = 'Access Worldwide';

    /**
     * Purolator Canada
     */
    public const PUROLATOR_CA = 'Purolator Canada';

    /**
     * Sendle
     */
    public const SENDLE = 'Sendle';

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
