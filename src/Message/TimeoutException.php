<?php declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorType;

/**
 * Class TimeoutException - The exception used by this SDK when the configured/default client timeout is reached.
 *
 * @package ShipEngine\Message
 */
final class TimeoutException extends ShipEngineException
{
    /**
     * TimeoutException constructor - Instantiates a timeout exception.
     *
     * @param int $retryAfter The amount of time (in SECONDS) to wait before retrying the request.
     * @param string|null $source
     * @param string|null $requestId
     */
    public function __construct(
        int $retryAfter,
        string $source = null,
        ?string $requestId = null
    ) {
        parent::__construct(
            "The request took longer than the $retryAfter seconds allowed.",
            $requestId,
            $source,
            'System',
            'Timeout',
            'https://www.shipengine.com/docs/rate-limits'
        );
    }
}
