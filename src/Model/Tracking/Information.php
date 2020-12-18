<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

use ShipEngine\Util;

/**
 * Tracking information about a `Shipment`.
 *
 * @property string $carrier_code
 * @property string $tracking_number
 * @property \ShipEngine\Util\ISOString $estimated_delivery
 * @property array $events
 */
final class Information
{
    use Util\Getters;
    
    private string $tracking_number;
    private Util\ISOString $estimated_delivery;
    private array $events;

    public function __construct(
        string $tracking_number,
        Util\ISOString $estimated_delivery,
        array $events
    ) {
        $this->tracking_number = $tracking_number;
        $this->estimated_delivery = $estimated_delivery;
        usort($events, function (Event $a, Event $b) {
            return strcmp((string) $a->date_time, (string) $b->date_time);
        });
        $this->events = $events;
    }
    
    /**
     * Returns the latest event in $this->events.
     */
    public function latestEvent(): ?Event
    {
        if (count($this->events) > 0) {
            return array_slice($this->events, -1, 1)[0];
        }
        
        return null;
    }

    /**
     * Returns the $event->date_time of the first `Status::ACCEPTED` event.
     */
    public function shippedAt(): ?Util\ISOString
    {
        foreach ($this->events as $event) {
            if ($event->status == Status::ACCEPTED) {
                return $event->date_time;
            }
        }
        
        return null;
    }

    /**
     * Returns the $event->date_time of the last `Status::DELIVERED` event.
     */
    public function deliveredAt(): ?Util\ISOString
    {
        $reversed = array_reverse($this->events);
        foreach ($this->events as $event) {
            if ($event->status == Status::DELIVERED) {
                return $event->date_time;
            }
        }
        
        return null;
    }
}
