<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use ShipEngine\Util\Constants\EventType;

final class RequestSentEvent extends ShipEngineEvent
{
    /**
     * @const REQUEST_SENT
     */
    const REQUEST_SENT = 'request_sent';

    private string $request_id;

    private string $url;

    private array $headers;

    private string $body;

    private int $retry;

    private int $timeout;

    public function __construct(
        string $message,
        string $request_id,
        string $url,
        array $headers,
        string $body,
        int $retry,
        int $timeout
    ) {
        parent::__construct(self::REQUEST_SENT, $message);
        $this->request_id = $request_id;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
        $this->retry = $retry;
        $this->timeout = $timeout;
    }
}
