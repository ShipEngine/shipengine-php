<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Util;

/**
 * `TrackingData` Type to be returned by the *trackPackage()* convenience method.
 *
 * @package ShipEngine\Model\Package
 * @property array $information
 * @property array $messages
 */
final class TrackingData implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var array
     */
    private array $information;

    /**
     * @var array
     */
    private array $messages;

    /**
     * TrackingData constructor.
     * @param array $information
     * @param array $messages
     */
    public function __construct(
        array $information,
        array $messages
    ) {
        $this->information = $information;
        $this->messages = $messages;
    }

    /**
     * Return a JsonSerialized string representation of the `TrackingData` Type.
     *
     * ```json
     * {
     * "information": {
     * "tracking_number": "voluptate",
     * "estimated_delivery": "2021-03-04T23:04:48.393Z",
     * "events": [
     * {
     * "date_time": "2021-03-04T23:04:48.393Z",
     * "status": "DELIVERED",
     * "description": "amet consequat sint anim",
     * "carrier_status_code": "consectetur",
     * "carrier_detail_code": "irure",
     * "location": {
     * "city_locality": "Boston",
     * "state_province": "MA",
     * "postal_code": "02215",
     * "country_code": "US",
     * "coordinates": {
     * "latitude": 42.346268,
     * "longitude": -71.09576
     * }
     * }
     * }
     * ]
     * },
     * "messages": {
     * "warnings": [
     * "do labore laborum pariatur"
     * ],
     * "errors": [],
     * "info": [
     * "voluptate ea",
     * "culpa fugiat deserunt",
     * "nisi adipisicing laborum dolor"
     * ]
     * }
     * }
     * ```
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'information' => $this->information,
            'messages' => $this->messages
        ], JSON_PRETTY_PRINT);
    }
}
