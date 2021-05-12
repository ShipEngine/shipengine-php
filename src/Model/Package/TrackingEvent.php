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
    public IsoString $dateTime;

    /**
     * The current date-time of the tracking event per the carrier records.
     *
     * @var IsoString
     */
    public IsoString $carrierDateTime;

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
    public ?string $carrierStatusCode;

    /**
     * The carrier defined detail code.
     *
     * @var string|null
     */
    public ?string $carrierDetailCode;

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
        $this->dateTime = new IsoString($events['timestamp']);
        $this->carrierDateTime = new IsoString($events['carrierTimestamp']);
        $this->status = $events['status'];
        $this->description = null ?? $events['description'];
        $this->carrierStatusCode = null ?? $events['carrierStatusCode'];
        $this->carrierDetailCode = isset($events['carrierDetailCode']) ? $events['carrierDetailCode'] : null;
        $this->signer = isset($events['signer']) ? $events['signer'] : null;
        $this->location = isset($events['location']) ? new Location($events['location']) : null;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
            'dateTime' => (string) $this->dateTime,
            'carrierDateTime' => (string) $this->carrierDateTime,
            'status' => $this->status,
            'description' => $this->description,
            'carrierStatusCode' => $this->carrierStatusCode,
            'carrierDetailCode' => $this->carrierDetailCode,
            'signer' => $this->signer,
            'location' => $this->location
        ];
    }
}
