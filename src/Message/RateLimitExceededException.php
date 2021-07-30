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
     * The amount of time (in SECONDS) to wait before retrying the request.
     *
     * @var \DateInterval
     */
    public \DateInterval $retryAfter;

    /**
     * RateLimitExceededException constructor - Instantiates a server-side error.
     *
     * @param \DateInterval $retryAfter
     * @param string|null $source
     * @param string|null $requestId
     */
    public function __construct(
        \DateInterval $retryAfter,
        string $source = null,
        ?string $requestId = null
    ) {
        parent::__construct(
            'You have exceeded the rate limit.',
            $requestId,
            $source,
            'System',
            'Rate Limit Exceeded',
            'https://www.shipengine.com/docs/rate-limits'
        );

        $this->retryAfter = $retryAfter;
    }
}
