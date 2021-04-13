<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateTime;
use ShipEngine\Util;

class ShipEngineEvent
{
    use Util\Getters;

    public DateTime $timestamp;
    private string $type;
    private string $message;

    /**
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
