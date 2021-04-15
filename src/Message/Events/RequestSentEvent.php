<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;
use ShipEngine\Util;

/**
 * Class RequestSentEvent
 * @package ShipEngine\Message\Events
 */
final class RequestSentEvent extends ShipEngineEvent
{
    use Util\Getters;

    /**
     * The event name for the RequestSentEvent.
     *
     * @const REQUEST_SENT
     */
    const REQUEST_SENT = 'request_sent';

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
     * The timeout for requests in seconds.
     *
     * @var DateInterval
     * @link https://www.php.net/manual/en/class.dateinterval.php
     */
    private DateInterval $timeout;

    /**
     * RequestSentEvent constructor - this event is emitted when a request is sent from the
     * ShipEngineClient to the target server.
     *
     * @param string $message
     * @param string $request_id
     * @param string $url
     * @param array $headers
     * @param array $body
     * @param int $retry
     * @param DateInterval $timeout
     */
    public function __construct(
        string $message,
        string $request_id,
        string $url,
        array $headers,
        array $body,
        int $retry,
        DateInterval $timeout
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
