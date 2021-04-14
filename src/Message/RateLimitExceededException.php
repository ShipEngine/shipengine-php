<?php declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util;
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
    use Util\Getters;

    private int $retryAfter;

    public function __construct(
        int $retryAfter,
        ?string $request_id = null
    ) {
        parent::__construct(
            'You have exceeded the rate limit.',
            $request_id,
            '',
            ErrorType::SYSTEM,
            ErrorCode::RATE_LIMIT_EXCEEDED,
            'https://www.shipengine.com/docs/rate-limits'
        );

        $this->retryAfter = $retryAfter;
    }
}
