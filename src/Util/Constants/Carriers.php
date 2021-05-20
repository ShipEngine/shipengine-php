<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

use ShipEngine\Message\ShipEngineException;

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
    public const FEDEX = 'fedex';

    /**
     * UPS - United Parcel Service
     *
     * @link https://www.ups.com/us/en/about/sites.page
     */
    public const UPS = 'ups';

    /**
     * USPS - United State Postal Service
     */
    public const USPS = 'usps';

    /**
     * USPS services via Stamps.com
     * @link https://www.stamps.com/
     */
    public const STAMPS_COM = 'stamps_com';

    /**
     * DHL Express
     */
    public const DHL_EXPRESS = 'dhl_express';

    /**
     * DHL ECommerce
     */
    public const DHL_GLOBAL_MAIL = 'dhl_global_mail';

    /**
     * Canada Post
     */
    public const CANADA_POST = 'canada_post';

    /**
     * Australia Post
     */
    public const AUSTRALIA_POST = 'australia_post';

    /**
     * First Mile
     */
    public const FIRSTMILE = 'firstmile';

    /**
     * Asendia
     */
    public const ASENDIA = 'asendia';

    /**
     * OnTrac
     */
    public const ONTRAC = 'ontrac';

    /**
     * APC
     */
    public const APC = 'apc';

    /**
     * Newgistics
     */
    public const NEWGISTICS = 'newgistics';

    /**
     * Globegistics
     */
    public const GLOBEGISTICS = 'globegistics';

    /**
     * RR Donnelley
     */
    public const RR_DONNELLEY = 'rr_donnelley';

    /**
     * IMEX
     */
    public const IMEX = 'imex';

    /**
     * Access Worldwide
     */
    public const ACCESS_WORLDWIDE = 'access_worldwide';

    /**
     * Purolator Canada
     */
    public const PUROLATOR_CA = 'purolator_ca';

    /**
     * Sendle
     */
    public const SENDLE = 'sendle';

    /**
     * Verify that the passed in uppercase carrier name is a constant that exists on this class.
     *
     * @param string $upperCaseCarrierCode
     * @return bool
     */
    public static function doesCarrierExist(string $upperCaseCarrierCode): bool
    {
        return defined("ShipEngine\Util\Constants\Carriers::$upperCaseCarrierCode");
    }

    /**
     * Verify that the passed in uppercase carrier code is a constant that exists on this class
     * and returns its value.
     *
     * @param string $upperCaseCarrierCode
     * @return mixed
     */
    public static function getCarrierCode(string $upperCaseCarrierCode)
    {
        if (self::doesCarrierExist($upperCaseCarrierCode) === true) {
            return constant("ShipEngine\Util\Constants\Carriers::$upperCaseCarrierCode");
        }

        throw new ShipEngineException(
            "CarrierCode [$upperCaseCarrierCode] is not a constant in this class object."
        );
    }
}
