<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

/**
 * Class RPCMethods, this class provides constants use for RPC methods.
 *
 * @package ShipEngine\Util
 */
final class RPCMethods
{
    /**
     * Validate an address.
     */
    public const ADDRESS_VALIDATE = 'address/validate';

    /**
     * Track a package.
     */
    public const PACKAGE_TRACK = 'package/track';

    /**
     * Create a tag.
     */
    public const CREATE_TAG = 'create/tag';

    /**
     * List all carriers connected to a given ShipEngine account.
     */
    public const LIST_CARRIER_ACCOUNTS = 'carrierAccounts/list';

    /**
     * Track a given package.
     */
    public const TRACK_PACKAGE = 'package/track';
}
