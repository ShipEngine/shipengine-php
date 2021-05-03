<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * Class TrackPackageResult
 * @package ShipEngine\Model\Package
 */
final class TrackPackageResult implements \JsonSerializable
{
    /**
     * The shipment object that is related to the tracking data and is returned from ShipEngine API.
     *
     * @var Shipment|null
     */
    public ?Shipment $shipment;

    /**
     * A Package object representing the package data associated with a given shipment/tracking number.
     *
     * @var Package|null
     */
    public ?Package $package;

    /**
     * A list of tracking events that have occurred on a given shipment/tracking number, up to the
     * time of the request. Each event is of type `TrackingEvent`.
     *
     * @var array|null
     */
    public ?array $events;

    /**
     * This is the latest event to have occurred in the `$events` array.
     *
     * @return TrackingEvent
     */
    public function getLatestEvent(): TrackingEvent
    {
        return $this->events[array_key_last($this->events)];
    }

    /**
     * Returns `true` if there are any EXCEPTION events.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        foreach ($this->events as $event) {
            if ($event['status'] === 'EXCEPTION') {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns **only** the EXCEPTION events.
     *
     * @return array
     */
    public function getErrors(): array
    {
        $errors = array();
        foreach ($this->events as $event) {
            if ($event['status'] === 'EXCEPTION') {
                $errors[] = $event;
            }
        }
        return $errors;
    }

    /**
     * TrackPackageResult constructor. This object is the return type for the `trackPackage` method in
     * the `TrackPackageService`.
     *
     * @param array $api_response
     */
    public function __construct(array $api_response)
    {
        $this->shipment = $api_response['shipment'] ?? null;
        $this->package = $api_response['package'] ?? null;
        $this->events = $api_response['events'] ?? null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'shipment' => $this->shipment,
            'package' => $this->package,
            'events' => $this->events,
            'latest_event' => $this->getLatestEvent(),
            'has_errors' => $this->hasErrors(),
            'errors' => $this->getErrors()
        ];
    }
}
