<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;

/**
 * Class ResponseReceivedEvent
 * @package ShipEngine\Message\Events
 */
final class ResponseReceivedEvent extends ShipEngineEvent implements \JsonSerializable
{
    /**
     * The event name for the ResponseReceivedEvent.
     *
     * @const RESPONSE_RECEIVED
     */
    public const RESPONSE_RECEIVED = 'response_received';

    /**
     * The requestId that corresponds to the request that was sent when this event is emitted.
     *
     * @var string
     */
    public string $requestId;

    /**
     * This is the URL that the request was sent to.
     *
     * @var string
     */
    public string $url;

    /**
     * The response status code.
     *
     * @var int
     */
    public int $statusCode;

    /**
     * An array of request headers that was sent on the request that triggered this event.
     *
     * @var array
     */
    public array $headers;

    /**
     * An associative array representation of the response body.
     *
     * @var array
     */
    public array $body;

    /**
     * The current retry - this is used in the retry logic that the ShipEngineClient executes.
     *
     * @var int
     */
    public int $retry;

    /**
     * This is the elapsed time between the `RequestSentEvent` and the
     * `ResponseReceivedEvent`.
     *
     * @var DateInterval
     * @link https://www.php.net/manual/en/class.dateinterval.php
     */
    public DateInterval $elapsed;

    /**
     * ResponseReceivedEvent constructor - this event is emitted when a response
     * is received by the ShipEngineClient, from the target server.
     *
     * @param string $message
     * @param string $requestId
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     * @param array $body
     * @param int $retry
     * @param DateInterval $elapsed
     */
    public function __construct(
        string $message,
        string $requestId,
        string $url,
        int $statusCode,
        array $headers,
        array $body,
        int $retry,
        DateInterval $elapsed
    ) {
        parent::__construct(self::RESPONSE_RECEIVED, $message);
        $this->requestId = $requestId;
        $this->url = $url;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->retry = $retry;
        $this->elapsed = $elapsed;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'requestId' => $this->requestId,
            'url' => $this->url,
            'statusCode' => $this->statusCode,
            'headers' => $this->headers,
            'body' => $this->body,
            'retry' => $this->retry,
            'elapsed' => $this->elapsed->f,
            'timestamp' => $this->timestamp,
        ];
    }
}
