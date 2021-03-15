<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

/**
 * Class TrackingData
 * @package ShipEngine\Model\Package
 */
final class TrackingData
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
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'information' => $this->information,
            'messages' => $this->messages
        ]);
    }
}
