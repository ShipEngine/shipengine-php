<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

class ShipEngineEvent
{
    private string $timestamp;
    private string $type;
    private string $message;

    /**
     * ShipEngineEvent constructor.
     * @param string $timestamp
     * @param string $type
     * @param string $message
     */
    public function __construct(string $timestamp, string $type, string $message)
    {
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->message = $message;
    }
}
