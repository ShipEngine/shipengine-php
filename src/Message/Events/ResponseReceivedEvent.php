<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;
use ShipEngine\Util;

/**
 * Class ResponseReceivedEvent
 * @package ShipEngine\Message\Events
 */
final class ResponseReceivedEvent extends ShipEngineEvent
{
    use Util\Getters;

    /**
     * The event name for the ResponseReceivedEvent.
     *
     * @const RESPONSE_RECEIVED
     */
    const RESPONSE_RECEIVED = 'response_received';

    /**
     * The request_id that corresponds to the request that was sent when this event is emitted.
     *
     * @var string
     */
    private string $request_id;

    /**
     * This is the URL that the request was sent to.
     *
     * @var string
     */
    private string $url;

    /**
     * The response status code.
     *
     * @var int
     */
    private int $status_code;

    /**
     * An array of request headers that was sent on the request that triggered this event.
     *
     * @var array
     */
    private array $headers;

    /**
     * An associative array representation of the response body.
     *
     * @var array
     */
    private array $body;

    /**
     * The current retry - this is used in the retry logic that the ShipEngineClient executes.
     *
     * @var int
     */
    private int $retry;

    /**
     * This is the elapsed time between the `RequestSentEvent` and the
     * `ResponseReceivedEvent`.
     *
     * @var DateInterval
     * @link https://www.php.net/manual/en/class.dateinterval.php
     */
    private DateInterval $elapsed;

    /**
     * ResponseReceivedEvent constructor - this event is emitted when a response
     * is received by the ShipEngineClient, from the target server.
     *
     * @param string $message
     * @param string $request_id
     * @param string $url
     * @param int $status_code
     * @param array $headers
     * @param array $body
     * @param int $retry
     * @param DateInterval $elapsed
     */
    public function __construct(
        string $message,
        string $request_id,
        string $url,
        int $status_code,
        array $headers,
        array $body,
        int $retry,
        DateInterval $elapsed
    ) {
        parent::__construct(self::RESPONSE_RECEIVED, $message);
        $this->request_id = $request_id;
        $this->url = $url;
        $this->status_code = $status_code;
        $this->headers = $headers;
        $this->body = $body;
        $this->retry = $retry;
        $this->elapsed = $elapsed;
    }
}
