<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Util\IsoString;

/**
 * Class TrackingEvent
 * @package ShipEngine\Model\Package
 */
final class TrackingEvent implements \JsonSerializable
{
    /**
     * The current date-time of the tracking event.
     *
     * @var IsoString
     */
    public IsoString $date_time;

    /**
     * The current date-time of the tracking event per the carrier records.
     *
     * @var IsoString
     */
    public IsoString $carrier_date_time;

    /**
     * The current status of the tracking event.
     *
     * @var string
     */
    public string $status;

    /**
     * The carrier status description of the tracking event.
     *
     * @var string|null
     */
    public ?string $description;

    /**
     * The carrier defined status code.
     *
     * @var string|null
     */
    public ?string $carrier_status_code;

    /**
     * The carrier defined detail code.
     *
     * @var string|null
     */
    public ?string $carrier_detail_code;

    /**
     * The signer or person who singed the package if there is record of one.
     *
     * @var string|null
     */
    public ?string $signer;

    /**
     * The location details of where the current tracking event occurred.
     *
     * @var Location|null
     */
    public ?Location $location;

    /**
     * TrackingEvent constructor.
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->date_time = new IsoString($events['date_time']);
        $this->carrier_date_time = new IsoString($events['carrier_date_time']);
        $this->status = $events['status'];
        $this->description = null ?? $events['description'];
        $this->carrier_status_code = null ?? $events['carrier_status_code'];
        $this->carrier_detail_code = null ?? $events['carrier_detail_code'];
        $this->signer = null ?? $events['signer'];
        $this->location = isset($events['location']) ? new Location($events['location']) : null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'date_time' => $this->date_time,
            'carrier_date_time' => $this->carrier_date_time,
            'status' => $this->status,
            'description' => $this->description,
            'carrier_status_code' => $this->carrier_status_code,
            'carrier_detail_code' => $this->carrier_detail_code,
            'signer' => $this->signer,
            'location' => $this->location
        ];
    }
}
