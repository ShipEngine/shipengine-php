<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * `PackageTrackingResult` Type to be returned by the *PackageTrackingService*.
 *
 * @package ShipEngine\Model\Package
 * @property array $information
 * @property array $messages
 */
final class PackageTrackingResult implements \JsonSerializable
{
    /**
     * @var array
     */
    private array $information;

    /**
     * @var array
     */
    private array $messages;

    /**
     * PackageTrackingResult constructor.
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
     * Return a JsonSerialized string representation of the `PackageTrackingResult` Type.
     *
     * <code>
     * {
     *  "information": {
     *      "tracking_number": "voluptate",
     *      "estimated_delivery": "2021-03-04T23:04:48.393Z",
     *      "events": [
     *          {
     *              "date_time": "2021-03-04T23:04:48.393Z",
     *              "status": "DELIVERED",
     *              "description": "amet consequat sint anim",
     *              "carrier_status_code": "consectetur",
     *              "carrier_detail_code": "irure",
     *              "location": {
     *              "city_locality": "Boston",
     *              "state_province": "MA",
     *              "postal_code": "02215",
     *              "country_code": "US",
     *              "coordinates": {
     *              "latitude": 42.346268,
     *              "longitude": -71.09576
     *          }
     *      }
     *    }
     * ]
     * },
     * "messages": {
     *  "warnings": [
     *      "do labore laborum pariatur"
     *  ],
     *  "errors": [],
     *  "info": [
     *      "voluptate ea",
     *      "culpa fugiat deserunt",
     *      "nisi adipisicing laborum dolor"
     *      ]
     *  }
     * }
     * </code>
     */
    public function jsonSerialize()
    {
        return [
            'information' => $this->information,
            'messages' => $this->messages
        ];
    }
}
