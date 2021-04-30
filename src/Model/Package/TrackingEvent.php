<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use DateTime;
use ShipEngine\Util;

/**
 * Class TrackingEvent
 * @package ShipEngine\Model\Package
 */
final class TrackingEvent implements \JsonSerializable
{
    use Util\Getters;

    /**
     * The current date-time of the tracking event.
     *
     * @var DateTime
     */
    private DateTime $date_time;

    /**
     * The current date-time of the tracking event per the carrier records.
     *
     * @var DateTime
     */
    private DateTime $carrier_date_time;

    /**
     * The current status of the tracking event.
     *
     * @var string
     */
    private string $status;

    /**
     * The carrier status description of the tracking event.
     *
     * @var string|null
     */
    private ?string $description;

    /**
     * The carrier defined status code.
     *
     * @var string|null
     */
    private ?string $carrier_status_code;

    /**
     * The carrier defined detail code.
     *
     * @var string|null
     */
    private ?string $carrier_detail_code;

    /**
     * The signer or person who singed the package if there is record of one.
     *
     * @var string|null
     */
    private ?string $signer;

    /**
     * The location details of where the current tracking event occured.
     *
     * @var Location|null
     */
    private ?Location $location;

    /**
     * TrackingEvent constructor.
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->date_time = $events['date_time'];
        $this->carrier_date_time = $events['carrier_date_time'];
        $this->status = $events['status'];
        $this->description = $events['description'] ?? null;
        $this->carrier_status_code = $events['carrier_status_code'] ?? null;
        $this->carrier_detail_code = $events['carrier_detail_code'] ?? null;
        $this->signer = $events['signer'] ?? null;
        $this->location = $events['location'] ?? null;
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
