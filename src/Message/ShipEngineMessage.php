<?php declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util;

/**
 * Class ShipEngineMessage -- Generic Message.
 *
 * @package ShipEngine\Message
 */
class ShipEngineMessage
{
    use Util\Getters;

    /**
     * @var string
     */
    private string $message;

    /**
     * ShipEngineMessage Type constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
