<?php declare(strict_types=1);

namespace ShipEngine\Util\Constants;

/**
 * Indicates the type of an error. Think of this as a broad category rather
 * than a specific error.
 *
 * @link https://www.shipengine.com/docs/errors/codes/#error-type
 */
final class ErrorType
{
    /**
     * There is a problem with your account. This may be your ShipEngine account
     * or a third-party account. See the the error source to determine which
     * account needs your attention.
     */
    const ACCOUNT_STATUS = 'account_status';

    /**
     * A security error will occur if your API key is invalid or expired, or if
     * you attempt to perform an operation that is not permitted for your account.
     */
    const SECURITY = 'security';

    /**
     * Something is wrong with the input provided, such as missing a required field,
     * or an illegal value or combination of values. This error type always means
     * that some change needs to be made to the input before retrying.
     */
    const VALIDATION = 'validation';

    /**
     * There was a business rule violation. Business rules are requirements or
     * limitations of a system. If the error source is ShipEngine, then please
     * read the relevant documentation to find out what limitations or requirements
     * apply. Or contact our support for help. If the error source is the carrier
     * or order source, then ShipEngine support may still be able to help clarify
     * the problem or propose a solution, or you may need to contact the third-party
     * for assistance.
     */
    const BUSINESS_RULES = 'business_rules';

    /**
     * An unknown or unexpected error occurred in our system. Or an error occurred
     * that has not yet been assigned a specific error_type. If you receive
     * persistent system errors, then please contact our support or check our API
     * status page to see if there's a known issue.
     */
    const SYSTEM = 'system';
}
