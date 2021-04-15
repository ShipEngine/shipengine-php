<?php declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorType;

/**
 * This error occurs when a request to ShipEngine API is blocked due to the
 * rate limit being exceeded.
 *
 * @package ShipEngine\Message
 */
final class RateLimitExceededException extends ShipEngineException
{
    /**
     * The amount of time (in milliseconds) to wait before retrying the request.
     *
     * @var int
     */
    public int $retryAfter;

    /**
     * RateLimitExceededException constructor - Instantiates a server-side error.
     *
     * @param int $retryAfter
     * @param string|null $source
     * @param string|null $request_id
     */
    public function __construct(
        int $retryAfter,
        string $source = null,
        ?string $request_id = null
    ) {
        parent::__construct(
            'You have exceeded the rate limit.',
            $request_id,
            $source,
            ErrorType::SYSTEM,
            ErrorCode::RATE_LIMIT_EXCEEDED,
            'https://www.shipengine.com/docs/rate-limits'
        );

        $this->retryAfter = $retryAfter;
    }
}
