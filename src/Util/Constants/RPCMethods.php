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
    public const ADDRESS_VALIDATE = 'address.validate.v1';

    /**
     * Create a tag.
     */
    public const CREATE_TAG = 'create.tag.v1';

    /**
     * List all carriers connected to a given ShipEngine account.
     */
    public const LIST_CARRIERS = 'carrier.listAccounts.v1';

    /**
     * Track a given package.
     */
    public const TRACK_PACKAGE = 'package.track.v1';
}
