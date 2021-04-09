<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use ShipEngine\Util\Constants\EventType;

/**
 * Class ResponseReceivedEvent
 * @package ShipEngine\Message\Events
 */
final class ResponseReceivedEvent extends ShipEngineEvent
{
    /**
     * @const RESPONSE_RECEIVED
     */
    const RESPONSE_RECEIVED = 'response_received';

    /**
     * @var string
     */
    private string $request_id;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var int
     */
    private int $status_code;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var string
     */
    private string $body;

    /**
     * @var int
     */
    private int $retry;

    /**
     * @var int
     */
    private int $elapsed;

    /**
     * ResponseReceivedEvent constructor.
     *
     * @param string $message
     * @param string $request_id
     * @param string $url
     * @param int $status_code
     * @param array $headers
     * @param string $body
     * @param int $retry
     * @param int $elapsed
     */
    public function __construct(
        string $message,
        string $request_id,
        string $url,
        int $status_code,
        array $headers,
        string $body,
        int $retry,
        int $elapsed
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
