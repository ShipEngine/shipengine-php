<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

final class ShipEngineEventListener
{
    public function onRequestSent(RequestSentEvent $event)
    {
        return $event;
    }

    public function onResponseReceived(ResponseReceivedEvent $event)
    {
        return $event;
    }
}
