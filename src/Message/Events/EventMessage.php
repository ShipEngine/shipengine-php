<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use ShipEngine\Message\ShipEngineException;

/**
 * Class EventMessage - This class houses helper methods related to event messages.
 *
 * @package ShipEngine\Message\Events
 */
final class EventMessage
{
    /**
     * A method to dynamically create an event message based on the $messageType being passed in.
     *
     * @param string $method RPC method name.
     * @param string $baseUri The base url of the client.
     * @param string $messageType The type of event message to be returned.
     * @return string
     */
    public static function newEventMessage(string $method, string $baseUri, string $messageType): string
    {
        switch ($messageType) {
            case 'base_message':
                return "Calling the ShipEngine $method API at $baseUri";
            case 'retry_message':
                return "Retrying the ShipEngine $method API at $baseUri";
            default:
                throw new ShipEngineException("Message type [$messageType] is not a valid type of message");
        }
    }
}
