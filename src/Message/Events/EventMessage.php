<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use ShipEngine\Message\ShipEngineException;

final class EventMessage
{
    public static function newEventMessage(string $method, string $baseUri, string $messageType): string
    {
//        $message = null;

        switch ($messageType) {
            case 'base_message':
                return "Calling the ShipEngine $method API at $baseUri";
            case 'retry_message':
                return "Retrying the ShipEngine $method API at $baseUri";
            default:
                throw new ShipEngineException("Message type [$messageType] is not a valid type of message");
        }

//        if ($messageType === 'base_message') {
//            $message = "Calling the ShipEngine $method API at $baseUri";
//        } elseif ($messageType === 'retry_message') {
//            $message = "Retrying the ShipEngine $method API at $baseUri";
//        }
//        return $message;
    }
}
