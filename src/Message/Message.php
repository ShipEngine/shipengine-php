<?php declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util;

/**
 * Generic message.
 */
class Message
{
    use Util\Getters;
    
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
