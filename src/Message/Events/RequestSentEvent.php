<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

final class RequestSentEvent extends ShipEngineEvent
{
    private string $request_id;

    private string $url;

    private array $headers;

    private string $body;

    private int $retry;

    private int $timeout;

    public function __construct(
        string $timestamp,
        string $type,
        string $message,
        string $request_id,
        string $url,
        array $headers,
        string $body,
        int $retry,
        int $timeout
    ) {
        parent::__construct($timestamp, $type, $message);
        $this->request_id = $request_id;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
        $this->retry = $retry;
        $this->timeout = $timeout;
    }
}
