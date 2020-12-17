<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

use ShipEngine\Util;

/**
 * An event or status change that occurred while processing a `Shipment`.
 *
 * @property \ShipEngine\Util\ISOString $date_time
 * @property string $status
 * @property string $description
 * @property string $carrier_status_code
 * @property string $carrier_detail_code
 * @property \ShipEngine\Model\Tracking\Location $location
 * @property array $notes
 * @property ?string $signer
 */
final class Event
{
    use Util\Getters;
    
    private Util\ISOString $date_time;
    private string $status;
    private string $description;
    private string $carrier_status_code;
    private string $carrier_detail_code;
    private array $notes;
    private ?Location $location;
    private ?string $signer;

    public function __construct(
        Util\ISOString $date_time,
        string $status,
        string $description,
        string $carrier_status_code,
        string $carrier_detail_code,
        array $notes,
        Location $location = null,
        ?string $signer = null
    ) {
        $this->date_time = $date_time;
        $this->status = $status;
        $this->description = $description;
        $this->carrier_status_code = $carrier_status_code;
        $this->carrier_detail_code = $carrier_detail_code;
        $this->notes = $notes;
        $this->location = $location;
        $this->signer = $signer;
    }
}
