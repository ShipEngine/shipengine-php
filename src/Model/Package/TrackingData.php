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
