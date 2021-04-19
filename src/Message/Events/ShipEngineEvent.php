<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateTime;
use ShipEngine\Util;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The events that are emitted by the ShipEngine SDK
 */
class ShipEngineEvent extends Event
{
    use Util\Getters;

    /**
     * The timestamp of when the event was emitted.
     *
     * @var DateTime
     */
    public DateTime $timestamp;

    /**
     * The type of event being emitted.
     *
     * @var string
     */
    private string $type;

    /**
     * The event message to provide context to the underlying event.
     *
     * @var string
     */
    private string $message;

    /**
     * Instantiates events - all other events inherit from this class.
     *
     * ShipEngineEvent constructor.
     * @param string $type
     * @param string $message
     */
    public function __construct(string $type, string $message)
    {
        $this->timestamp = new DateTime();
        $this->type = $type;
        $this->message = $message;
    }
}