<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

/**
 * Class ShipEngineEventListener - A default **PSR-14** event listener to consume the events
 * emitted by this SDK.
 *
 * @package ShipEngine\Message\Events
 */
final class ShipEngineEventListener
{
    /**
     * Callback to handle/consume the **RequestSentEvent** whenever it is emitted by the ShipEngine SDK.
     *
     * @param RequestSentEvent $event
     * @return RequestSentEvent
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        return $event;
    }

    /**
     * Callback to handle/consume the **ResponseReceivedEvent** whenever it is emitted by the ShipEngine SDK.
     *
     * @param ResponseReceivedEvent $event
     * @return ResponseReceivedEvent
     */
    public function onResponseReceived(ResponseReceivedEvent $event)
    {
        return $event;
    }
}
