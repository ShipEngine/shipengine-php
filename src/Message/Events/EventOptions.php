<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;

/**
 * Class EventOptions
 *
 * @package ShipEngine\Message\Events
 */
final class EventOptions
{
    /**
     * Event message to be emitted.
     *
     * @var string|null
     */
    public ?string $message;

    /**
     * The requestId that corresponds to the request that was sent when this event is emitted.
     *
     * @var string|null
     */
    public ?string $id;

    /**
     * @var string|null
     */
    public ?string $baseUri;

    /**
     * The request or response body content as an associative array.
     *
     * @var array|null
     */
    public ?array $body;

    /**
     * An array of request headers that was sent on the request that triggered this event.
     *
     * @var array|null
     */
    public ?array $requestHeaders;

    /**
     * @var array|null
     */
    public ?array $responseHeaders;

    /**
     * The response status code.
     *
     * @var int|null
     */
    public ?int $statusCode;

    /**
     * The current retry - this is used in the retry logic that the ShipEngineClient executes.
     *
     * @var int|null
     */
    public ?int $retry;

    /**
     * @var DateInterval|null
     */
    public ?DateInterval $timeout;

    /**
     * This is the elapsed time between the `RequestSentEvent` and the
     * `ResponseReceivedEvent`.
     *
     * @var DateInterval|null
     * @link https://www.php.net/manual/en/class.dateinterval.php     */
    public ?DateInterval $elapsed;

    /**
     * EventOptions constructor - To be used as the main argument in the **ShipEngineEvent::emitEvent()** method.
     *
     * @param array $eventData
     */
    public function __construct(array $eventData)
    {
        $this->message = isset($eventData['message']) ? $eventData['message'] : null;
        $this->id = isset($eventData['id']) ? $eventData['id'] : null;
        $this->baseUri = isset($eventData['baseUri']) ? $eventData['baseUri'] : null;
        $this->requestHeaders = isset($eventData['requestHeaders']) ? $eventData['requestHeaders'] : null;
        $this->responseHeaders = isset($eventData['responseHeaders']) ? $eventData['responseHeaders'] : null;
        $this->statusCode = isset($eventData['statusCode']) ? $eventData['statusCode'] : null;
        $this->body = isset($eventData['body']) ? $eventData['body'] : null;
        $this->retry = isset($eventData['retry']) ? $eventData['retry'] : null;
        $this->timeout = isset($eventData['timeout']) ? $eventData['timeout'] : null;
        $this->elapsed = isset($eventData['elapsed']) ? $eventData['elapsed'] : null;
    }
}
