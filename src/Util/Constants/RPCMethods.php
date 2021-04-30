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
     * @const ADDRESS_VALIDATE Validate an address.
     */
    public const ADDRESS_VALIDATE = 'address/validate';

    /**
     * @const PACKAGE_TRACK Track a package.
     */
    public const PACKAGE_TRACK = 'package/track';

    /**
     * @const CREATE_TAG Create a tag.
     */
    public const CREATE_TAG = 'create/tag';

    /**
     * @const LIST_CARRIERS List all carriers connected to a given ShipEngine account.
     */
    public const LIST_CARRIER_ACCOUNTS = 'carrierAccounts/list';

    /**
     * @const TRACK_PACKAGE Track a given package.
     */
    public const TRACK_PACKAGE = 'package/track';
}
