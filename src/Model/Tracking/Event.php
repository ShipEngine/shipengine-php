<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

/**
 * An event or status change that occurred while processing a `Shipment`.
 *
 * @property \ShipEngine\Util\IsoString $date_time
 * @property string $status
 * @property string $description
 * @property string $carrier_status_code
 * @property string $carrier_detail_code
 * @property \ShipEngine\Model\Tracking\Location $location
 * @property ?string $signer
 */
final class Event
{
    use \ShipEngine\Message\Wrapper;
    use \ShipEngine\Util\Getters;
    
    private \ShipEngine\Util\IsoString $date_time;
    private string $status;
    private string $description;
    private string $carrier_status_code;
    private string $carrier_detail_code;
    private array $notes;
    private ?\ShipEngine\Model\Tracking\Location $location;
    private ?string $signer;

    public function __construct(
        \ShipEngine\Util\IsoString $date_time,
        string $status,
        string $description,
        string $carrier_status_code,
        string $carrier_detail_code,
        array $messages = array(),
        \ShipEngine\Model\Tracking\Location $location = null,
        ?string $signer = null
    ) {
        $this->date_time = $date_time;
        $this->status = $status;
        $this->description = $description;
        $this->carrier_status_code = $carrier_status_code;
        $this->carrier_detail_code = $carrier_detail_code;
        $this->messages = $messages;
        $this->location = $location;
        $this->signer = $signer;
    }
}
