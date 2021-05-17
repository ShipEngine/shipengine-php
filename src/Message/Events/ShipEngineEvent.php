<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateTime;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\ShipEngineConfig;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The events that are emitted by the ShipEngine SDK
 */
class ShipEngineEvent extends Event
{
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
    public string $type;

    /**
     * The event message to provide context to the underlying event.
     *
     * @var string
     */
    public string $message;

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

    public static function emitEvent(string $eventType, $eventData, ShipEngineConfig $config): ShipEngineEvent
    {
        $dispatcher = new EventDispatcher();
        $shipengineEventListener = $config->eventListener;

        switch ($eventType) {
            case RequestSentEvent::REQUEST_SENT:
                $requestSentEvent = new RequestSentEvent(
                    $eventData->message,
                    $eventData->id,
                    $eventData->baseUri,
                    $eventData->requestHeaders,
                    $eventData->body,
                    $eventData->retry,
                    $eventData->timeout,
                );

                $dispatcher->addListener(
                    $requestSentEvent::REQUEST_SENT,
                    [$shipengineEventListener, 'onRequestSent']
                );

                $dispatcher->dispatch($requestSentEvent, $requestSentEvent::REQUEST_SENT);
                return $requestSentEvent;
            case ResponseReceivedEvent::RESPONSE_RECEIVED:
                $responseReceivedEvent = new ResponseReceivedEvent(
                    $eventData->message,
                    $eventData->id,
                    $eventData->baseUri,
                    $eventData->statusCode,
                    $eventData->responseHeaders,
                    $eventData->body,
                    $eventData->retry,
                    $eventData->elapsed
                );

                $dispatcher->addListener(
                    $responseReceivedEvent::RESPONSE_RECEIVED,
                    [$shipengineEventListener, 'onResponseReceived']
                );
                $dispatcher->dispatch($responseReceivedEvent, $responseReceivedEvent::RESPONSE_RECEIVED);
                return $responseReceivedEvent;
            default:
                throw new ShipEngineException("Event type [$eventType] is not a valid type of event.");
        }

//        if ($eventType === RequestSentEvent::REQUEST_SENT) {
//            $requestSentEvent = new RequestSentEvent(
//                $eventData->message,
//                $eventData->id,
//                $eventData->baseUri,
//                $eventData->requestHeaders,
//                $eventData->body,
//                $eventData->retry,
//                $eventData->timeout,
//            );
//
//            $emittedEvent[] = $requestSentEvent;
//
//            $dispatcher->addListener(
//                $requestSentEvent::REQUEST_SENT,
//                [$shipengineEventListener, 'onRequestSent']
//            );
//
//            $dispatcher->dispatch($requestSentEvent, $requestSentEvent::REQUEST_SENT);
//        }
//
//        if ($eventType === ResponseReceivedEvent::RESPONSE_RECEIVED) {
//            $responseReceivedEvent = new ResponseReceivedEvent(
//                $eventData->message,
//                $eventData->id,
//                $eventData->baseUri,
//                $eventData->statusCode,
//                $eventData->headers,
//                $eventData->body,
//                $eventData->retry,
//                $eventData->elapsed
//            );
//
//            $emittedEvent[] = $responseReceivedEvent;
//
//            $dispatcher->addListener(
//                $responseReceivedEvent::RESPONSE_RECEIVED,
//                [$shipengineEventListener, 'onResponseReceived']
//            );
//            $dispatcher->dispatch($responseReceivedEvent, $responseReceivedEvent::RESPONSE_RECEIVED);
//            return $responseReceivedEvent;
//        }
    }
}
