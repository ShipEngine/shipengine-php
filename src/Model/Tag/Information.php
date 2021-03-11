<?php declare(strict_types=1);

namespace ShipEngine\Model\Tag;

/**
 * Class Information
 * @package ShipEngine\Model\Tag
 */
final class Information
{
    /**
     * @var string
     */
    private string $tracking_number;
    /**
     * @var string
     */
    private string $estimated_delivery;
    /**
     * @var array
     */
    private array $events;

    /**
     * Information Type constructor.
     *
     * @param $tracking_number
     * @param $estimated_delivery
     * @param $events
     */
    public function __construct(
        $tracking_number,
        $estimated_delivery,
        $events
    ) {
        $this->tracking_number = $tracking_number;
        $this->estimated_delivery = $estimated_delivery;
        $this->events = $events;
    }
}
